<?php
$db_server = "localhost";
$db_user = " ";
$db_pass = " ";
$db_name = " ";

// Database connection
$connect = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$connect) {
    die("Could not connect: " . mysqli_connect_error());
}
echo "You are connected successfully.<br>";

// Load JSON file
$filename = "data.json";
$data = file_get_contents($filename);

if ($data === false) {
    die("Error: Could not read JSON file.");
}

$array = json_decode($data, true);

if ($array === null) {
    die("Error decoding JSON: " . json_last_error_msg());
}

// Initialize SQL query strings
$query_gallery = "";
$query_categories = "";
$query_items = "";
$query_attributes = "";
$query_currency = "";
$query_prices = "";
$query_products = "";
$query_data = "";

// Process data from JSON
if (isset($array['data']['products'])) {
    foreach ($array['data']['products'] as $product) {
        $product_id = mysqli_real_escape_string($connect, $product['id'] ?? '');
        $name = mysqli_real_escape_string($connect, $product['name'] ?? '');
        $inStock = mysqli_real_escape_string($connect, isset($product['inStock']) && $product['inStock'] ? 1 : 0);
        $description = mysqli_real_escape_string($connect, $product['description'] ?? '');
        $category = mysqli_real_escape_string($connect, $product['category'] ?? '');
        $brand = mysqli_real_escape_string($connect, $product['brand'] ?? '');
        $typename = mysqli_real_escape_string($connect, $product['__typename'] ?? '');
        
        // Handle prices (one-to-one relationship)
        $product_price_amount = null;
        if (isset($product['prices'][0])) {
            $price = $product['prices'][0];
            $amount = mysqli_real_escape_string($connect, $price['amount'] ?? '');
            $currency_label = mysqli_real_escape_string($connect, $price['currency']['label'] ?? '');
            $currency_symbol = mysqli_real_escape_string($connect, $price['currency']['symbol'] ?? '');
            $currency_typename = mysqli_real_escape_string($connect, $price['currency']['__typename'] ?? '');

            // Insert into currency table if not exists
            $query_currency .= "INSERT IGNORE INTO currency (currency_label, symbol, __typename) 
                                VALUES ('$currency_label', '$currency_symbol', '$currency_typename');";

            // Insert price into prices table using INSERT IGNORE to avoid duplicates
            $query_prices .= "INSERT IGNORE INTO prices (prices_amount, currency_label, __typename) 
                              VALUES ('$amount', '$currency_label', 'Price');";
            
            // Set the product_price_amount to link with the product
            $product_price_amount = $amount;  // Keep track of the price reference
        }

        // Handle gallery
        if (isset($product['gallery']) && is_array($product['gallery'])) {
            foreach ($product['gallery'] as $gallery_url) {
                $gallery_id = md5($gallery_url); // Generate unique gallery ID
                $gallery_url_safe = mysqli_real_escape_string($connect, $gallery_url);

                // Insert into gallery table with products_id
                $query_gallery .= "INSERT INTO gallery (gallery_id, url, products_id) 
                                   VALUES ('$gallery_id', '$gallery_url_safe', '$product_id')
                                   ON DUPLICATE KEY UPDATE url='$gallery_url_safe', products_id='$product_id';";
            }
        }

        // Handle categories
        $typename_category = "Category";
        $query_categories .= "INSERT IGNORE INTO categories (categories_name, __typename) 
                               VALUES ('$category', '$typename_category');";

        // Link products to categories
        $query_data .= "INSERT INTO data (products_id, categories_name) 
                         VALUES ('$product_id', '$category') 
                         ON DUPLICATE KEY UPDATE categories_name='$category';";

        // Handle attributes and items linked to products
        if (isset($product['attributes'])) {
            foreach ($product['attributes'] as $attribute) {
                $attribute_id = mysqli_real_escape_string($connect, $attribute['id'] ?? '');
                $attribute_name = mysqli_real_escape_string($connect, $attribute['name'] ?? '');
                $attribute_type = mysqli_real_escape_string($connect, $attribute['type'] ?? '');
                $attribute_typename = mysqli_real_escape_string($connect, $attribute['__typename'] ?? '');

                // Insert attributes with products_id
                $query_attributes .= "INSERT INTO attributes (attributes_id, name, type, __typename, products_id) 
                                      VALUES ('$attribute_id', '$attribute_name', '$attribute_type', '$attribute_typename', '$product_id')
                                      ON DUPLICATE KEY UPDATE name='$attribute_name', type='$attribute_type', products_id='$product_id';";

                // Handle items for each attribute
                if (isset($attribute['items'])) {
                    foreach ($attribute['items'] as $item) {
                        $item_id = mysqli_real_escape_string($connect, $item['id'] ?? '');
                        $displayValue = mysqli_real_escape_string($connect, $item['displayValue'] ?? '');
                        $value = mysqli_real_escape_string($connect, $item['value'] ?? '');
                        $typename_item = mysqli_real_escape_string($connect, $item['__typename'] ?? '');

                        // Insert into items table
                        $query_items .= "INSERT INTO items (items_id, displayValue, value, __typename) 
                                         VALUES ('$item_id', '$displayValue', '$value', '$typename_item')
                                         ON DUPLICATE KEY UPDATE displayValue='$displayValue', value='$value';";

                        // Link item to attribute (one-to-many relationship)
                        $query_attributes .= "UPDATE attributes SET items_id = '$item_id' WHERE attributes_id = '$attribute_id';";
                    }
                }
            }
        }

        // Insert product into the products table with price reference (prices_amount)
        if (!empty($product_id) && $product_price_amount !== null) {
            $query_products .= "INSERT INTO products (products_id, name, inStock, description, category, brand, __typename, prices_amount) 
                                 VALUES ('$product_id', '$name', '$inStock', '$description', '$category', '$brand', '$typename', '$product_price_amount') 
                                 ON DUPLICATE KEY UPDATE name='$name', inStock='$inStock', description='$description', category='$category', brand='$brand', prices_amount='$product_price_amount';";
        }
    }
}

// Function to execute queries
function executeQueries($connect, $query, $successMessage, $errorMessage) {
    if (!empty($query)) {
        if (mysqli_multi_query($connect, $query)) {
            do {
                if ($result = mysqli_store_result($connect)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_more_results($connect) && mysqli_next_result($connect));
            echo "<h3>$successMessage</h3>";
        } else {
            echo "Error: $errorMessage - " . mysqli_error($connect);
        }
    }
}

// Execute all queries in the correct order (prices first, then products, etc.)
executeQueries($connect, $query_currency, "Currency data inserted successfully", "Error inserting into currency table");
executeQueries($connect, $query_prices, "Prices data inserted successfully", "Error inserting into prices table");
executeQueries($connect, $query_products, "Products data inserted successfully", "Error inserting into products table");
executeQueries($connect, $query_gallery, "Gallery data inserted successfully", "Error inserting into gallery table");
executeQueries($connect, $query_categories, "Categories data inserted successfully", "Error inserting into categories table");
executeQueries($connect, $query_data, "Data table inserted successfully", "Error inserting into data table");
executeQueries($connect, $query_items, "Items data inserted successfully", "Error inserting into items table");
executeQueries($connect, $query_attributes, "Attributes data inserted successfully", "Error inserting into attributes table");

// Close the database connection
mysqli_close($connect);
?>
