<?php

namespace GraphQL\Resolvers;

use Models\Product;

class ProductResolver
{
    /**
     * Resolve and return all products from the database.
     *
     * @return array
     */
    public static function resolveProducts(): array
    {
        $product = new Product();
        return $product->all(); // Fetch all products from the database
    }

    /**
     * Resolve attributes of a specific product.
     *
     * @param object $product
     * @return array
     */
    public static function resolveProductAttributes($product): array
    {
        try {
            return $product->attributes(); // Fetch attributes for the product
        } catch (\Exception $e) {
            error_log("Error fetching attributes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Resolve gallery data of a specific product.
     *
     * @param object $product
     * @return array
     */
    public static function resolveProductGallery($product): array
    {
        try {
            return $product->gallery(); // Fetch gallery data for the product
        } catch (\Exception $e) {
            error_log("Error fetching gallery: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Resolve pricing information of a specific product.
     *
     * @param object $product
     * @return array
     */
    public static function resolveProductPrices($product): array
    {
        try {
            return $product->prices(); // Fetch price data for the product
        } catch (\Exception $e) {
            error_log("Error fetching prices: " . $e->getMessage());
            return [];
        }
    }
}
?>
