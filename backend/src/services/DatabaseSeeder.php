<?php
namespace Services;

use Models\Product;
use Models\Price;
use Models\Currency;
use Models\Gallery;
use Models\Item;
use Models\DataTable;
use Models\Category;
use Models\Attribute;

class DatabaseSeeder {
    private $db;

    public function __construct($db) {
        $this->db = $db; // mysqli database connection
    }

    public function seed(array $data) {
        try {
            // Check if 'currency' exists; if not, use an empty array
            $currencies = $data['currency'] ?? [];
            $products = $data['products'] ?? [];
            $categories = $data['categories'] ?? [];
            $gallery = $data['gallery'] ?? [];
            $items = $data['items'] ?? [];
            $dataTable = $data['data'] ?? [];
            $attributes = $data['attributes'] ?? [];

            $this->seedCurrency($currencies);
            $this->seedPrices($products);
            $this->seedCategories($categories);
            $this->seedProducts($products);
            $this->seedGallery($gallery);
            $this->seedItems($items);
            $this->seedDataTable($dataTable);
            $this->seedAttributes($attributes);

            echo "Database seeding completed successfully.<br>";
        } catch (\Exception $e) {
            echo "Error during database seeding: " . $e->getMessage();
        }
    }

    private function seedCurrency(array $currencies) {
        foreach ($currencies as $currencyData) {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO currency (currency_label, symbol, __typename) VALUES (?, ?, ?)"
            );
            $stmt->bind_param('sss', $currencyData['label'], $currencyData['symbol'], $currencyData['__typename']);
            $stmt->execute();
        }

        echo "Currency data seeded successfully.<br>";
    }

    private function seedPrices(array $products) {
        foreach ($products as $productData) {
            if (isset($productData['prices'][0])) {
                $priceData = $productData['prices'][0];
                $stmt = $this->db->prepare(
                    "INSERT IGNORE INTO prices (prices_amount, currency_label, __typename) VALUES (?, ?, ?)"
                );
                $stmt->bind_param('sss', $priceData['amount'], $priceData['currency']['label'], $priceData['__typename']);
                $stmt->execute();
            }
        }

        echo "Prices data seeded successfully.<br>";
    }

    private function seedCategories(array $categories) {
        foreach ($categories as $categoryData) {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO categories (categories_name, __typename) VALUES (?, ?)"
            );
            $stmt->bind_param('ss', $categoryData['name'], $categoryData['__typename']);
            $stmt->execute();
        }

        echo "Categories seeded successfully.<br>";
    }

    private function seedProducts(array $products) {
        foreach ($products as $productData) {
            $stmt = $this->db->prepare(
                "INSERT INTO products (products_id, name, inStock, description, category, prices_amount, brand, __typename) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                'ssisssss',
                $productData['id'],
                $productData['name'],
                $productData['inStock'],
                $productData['description'],
                $productData['category'],
                $productData['prices'][0]['amount'],
                $productData['brand'],
                $productData['__typename']
            );
            $stmt->execute();
        }

        echo "Products data seeded successfully.<br>";
    }

    private function seedGallery(array $gallery) {
        foreach ($gallery as $galleryData) {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO gallery (gallery_id, url, products_id) VALUES (?, ?, ?) "
            );
            $galleryId = md5($galleryData['url']);
            $stmt->bind_param('sss', $galleryId, $galleryData['url'], $galleryData['productId']);
            $stmt->execute();
        }

        echo "Gallery data seeded successfully.<br>";
    }

    private function seedItems(array $items) {
        foreach ($items as $itemData) {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO items (items_id, displayValue, value, __typename) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param('ssss', $itemData['id'], $itemData['displayValue'], $itemData['value'], $itemData['__typename']);
            $stmt->execute();
        }

        echo "Items data seeded successfully.<br>";
    }

    private function seedDataTable(array $data) {
        foreach ($data as $dataRow) {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO data (products_id, categories_name) VALUES (?, ?)"
            );
            $stmt->bind_param('ss', $dataRow['products_id'], $dataRow['categories_name']);
            $stmt->execute();
        }

        echo "DataTable seeded successfully.<br>";
    }

    private function seedAttributes(array $attributes) {
        foreach ($attributes as $attribute) {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO attributes (attributes_id, items_id, name, type, __typename, products_id) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                'ssssss',
                $attribute['id'],
                $attribute['items_id'],
                $attribute['name'],
                $attribute['type'],
                $attribute['__typename'],
                $attribute['products_id']
            );
            $stmt->execute();
        }

        echo "Attributes data seeded successfully.<br>";
    }
}
?>