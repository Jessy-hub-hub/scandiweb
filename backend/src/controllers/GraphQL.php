<?php
namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use RuntimeException;
use Throwable;

// List of allowed origins
$allowedOrigins = ['http://localhost:3000', 'https://jessy-hub-hub.github.io'];

// If the request has an Origin header and it is in our allowed list, send it back.
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
    header("Vary: Origin");
} else {
    // Optionally, you can set a default allowed origin or reject the request.
    // header("Access-Control-Allow-Origin: https://jessy-hub-hub.github.io");
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400");
    exit(0);
}

class GraphQL
{
    public static function handle()
    {
        try {
            $dataFilePath = __DIR__ . '/../services/data/data.json';

            if (!file_exists($dataFilePath)) {
                throw new RuntimeException('JSON data file not found');
            }

            $jsonData = json_decode(file_get_contents($dataFilePath), true);

            if (!isset($jsonData['data'])) {
                throw new RuntimeException('Malformed JSON structure');
            }

            // Define Currency type
            $currencyType = new ObjectType([
                'name' => 'Currency',
                'fields' => [
                    'symbol' => ['type' => Type::string()],
                ],
            ]);

            // Define Price type
            $priceType = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'amount' => ['type' => Type::float()],
                    'currency' => ['type' => $currencyType],
                ],
            ]);

            // Define AttributeItem type
            $attributeItemType = new ObjectType([
                'name' => 'AttributeItem',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'displayValue' => ['type' => Type::string()],
                    'value' => ['type' => Type::string()],
                ],
            ]);

            // Define AttributeSet type
            $attributeSetType = new ObjectType([
                'name' => 'AttributeSet',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'name' => ['type' => Type::string()],
                    'type' => ['type' => Type::string()],
                    'items' => ['type' => Type::listOf($attributeItemType)],
                ],
            ]);

            // Define Product type
            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => ['type' => Type::id()],
                    'name' => ['type' => Type::string()],
                    'description' => ['type' => Type::string()],
                    'inStock' => ['type' => Type::boolean()],
                    'prices' => ['type' => Type::listOf($priceType)],
                    'attributes' => ['type' => Type::listOf($attributeSetType)],
                    'gallery' => ['type' => Type::listOf(Type::string())],
                    'category' => ['type' => Type::string()],
                    'productCard' => [
                        'type' => Type::string(),
                        'resolve' => function ($product) {
                            return 'product-' . strtolower(str_replace(' ', '-', $product['name']));
                        }
                    ],
                ],
            ]);

            // Define Category type
            $categoryType = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'name' => ['type' => Type::string()],
                    'products' => ['type' => Type::listOf($productType)],
                ],
            ]);

            // Define Cart Item type
            $cartItemType = new ObjectType([
                'name' => 'CartItem',
                'fields' => [
                    'productId' => ['type' => Type::id()],
                    'quantity' => ['type' => Type::nonNull(Type::int())],
                    'totalPrice' => ['type' => Type::float()],
                    'attributes' => [
                        'type' => new ObjectType([
                            'name' => 'CartItemAttributes',
                            'fields' => [
                                'size' => ['type' => Type::string()],
                                'color' => ['type' => Type::string()],
                            ],
                        ])
                    ],
                ],
            ]);

            // Define OrderProductInput input type
            $orderProductInputType = new InputObjectType([
                'name' => 'OrderProductInput',
                'fields' => [
                    'productId' => ['type' => Type::nonNull(Type::id())],
                    'quantity' => ['type' => Type::nonNull(Type::int())],
                    'totalPrice' => ['type' => Type::float()],
                ],
            ]);

            // Define OrderProduct output type
            $orderProductType = new ObjectType([
                'name' => 'OrderProduct',
                'fields' => [
                    'productId' => ['type' => Type::id()],
                    'quantity' => ['type' => Type::nonNull(Type::int())],
                    'totalPrice' => ['type' => Type::float()],
                ],
            ]);

            // Define Order type
            $orderType = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => ['type' => Type::id()],
                    'products' => ['type' => Type::listOf($orderProductType)],
                    'totalPrice' => ['type' => Type::float()],
                ],
            ]);

            // Define Query schema
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => function () use ($jsonData) {
                            return $jsonData['data']['products'] ?? [];
                        }
                    ],
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => function () use ($jsonData) {
                            return $jsonData['data']['categories'] ?? [];
                        }
                    ],
                    'cart' => [
                        'type' => Type::listOf($cartItemType),
                        'resolve' => function () use ($jsonData) {
                            return $jsonData['data']['cart'] ?? [];
                        }
                    ],
                ],
            ]);

            // Define Mutation schema for cart operations and order creation
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'addToCart' => [
                        'type' => $cartItemType,
                        'args' => [
                            'productId' => Type::nonNull(Type::id()),
                            'quantity' => Type::nonNull(Type::int()),
                            'attributes' => [
                                'type' => new ObjectType([
                                    'name' => 'AttributesInput',
                                    'fields' => [
                                        'size' => ['type' => Type::string()],
                                        'color' => ['type' => Type::string()],
                                    ],
                                ])
                            ],
                        ],
                        'resolve' => function ($root, $args) use (&$jsonData, $dataFilePath) {
                            $newCartItem = [
                                'productId' => $args['productId'],
                                'quantity' => $args['quantity'],
                                'attributes' => $args['attributes'],
                                'totalPrice' => 0,
                            ];
                            $jsonData['data']['cart'][] = $newCartItem;
                            file_put_contents($dataFilePath, json_encode($jsonData, JSON_PRETTY_PRINT));
                            return $newCartItem;
                        }
                    ],
                    'removeFromCart' => [
                        'type' => Type::string(),
                        'args' => [
                            'productId' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function ($root, $args) use (&$jsonData, $dataFilePath) {
                            $jsonData['data']['cart'] = array_filter(
                                $jsonData['data']['cart'],
                                fn($item) => $item['productId'] !== $args['productId']
                            );
                            file_put_contents($dataFilePath, json_encode($jsonData, JSON_PRETTY_PRINT));
                            return "Product removed from cart";
                        }
                    ],
                    'createOrder' => [
                        'type' => $orderType,
                        'args' => [
                            'products' => Type::nonNull(Type::listOf($orderProductInputType))
                        ],
                        'resolve' => function ($root, $args) use (&$jsonData, $dataFilePath) {
                            $orderProducts = $args['products'];
                            $total = 0;
                            foreach ($orderProducts as $product) {
                                $total += $product['totalPrice'];
                            }
                            $orderId = uniqid();
                            $newOrder = [
                                'id' => $orderId,
                                'products' => $orderProducts,
                                'totalPrice' => $total,
                            ];
                            if (!isset($jsonData['data']['orders'])) {
                                $jsonData['data']['orders'] = [];
                            }
                            $jsonData['data']['orders'][] = $newOrder;
                            // Clear the cart after order creation
                            $jsonData['data']['cart'] = [];
                            file_put_contents($dataFilePath, json_encode($jsonData, JSON_PRETTY_PRINT));
                            return $newOrder;
                        }
                    ],
                ],
            ]);

            // Create schema
            $schema = new Schema([
                'query' => $queryType,
                'mutation' => $mutationType,
            ]);

            $query = $variableValues = null;

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $query = $_GET['query'] ?? null;
                $variableValues = $_GET['variables'] ?? null;
                if (!$query) {
                    throw new RuntimeException('GraphQL query missing from URL');
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $rawInput = file_get_contents('php://input');
                if (!$rawInput) {
                    throw new RuntimeException('No payload received');
                }
                $input = json_decode($rawInput, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new RuntimeException('Malformed JSON payload');
                }
                $query = $input['query'];
                $variableValues = $input['variables'] ?? null;
            }

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => ['message' => $e->getMessage()]]);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($output);
    }
}
?>
