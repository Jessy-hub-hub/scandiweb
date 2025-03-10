<?php
namespace App\GraphQL\Resolvers;

class OrderResolver {
    public static function createOrder($args) {
        // Log input for debugging
        error_log("Mutation received args: " . print_r($args, true));

        $dataFilePath = __DIR__ . '/../services/data/data.json';

        // Validate input
        if (!isset($args['id'], $args['productId'], $args['quantity'], $args['totalPrice'])) {
            throw new \Exception("Invalid input.");
        }

        // Load existing data
        if (!file_exists($dataFilePath)) {
            throw new \RuntimeException('JSON data file not found.');
        }

        $jsonData = json_decode(file_get_contents($dataFilePath), true);

        // Check if JSON structure is valid
        if (!isset($jsonData['data']['orders'])) {
            $jsonData['data']['orders'] = [];
        }

        // Create a new order
        $newOrder = [
            'id' => $args['id'],
            'productId' => $args['productId'],
            'quantity' => $args['quantity'],
            'totalPrice' => $args['totalPrice'],
        ];

        // Add the new order to data
        $jsonData['data']['orders'][] = $newOrder;

        // Save to JSON file
        if (file_put_contents($dataFilePath, json_encode($jsonData, JSON_PRETTY_PRINT)) === false) {
            throw new \RuntimeException('Failed to save changes to JSON.');
        }

        // Return the created order
        return $newOrder;
    }
}
?>