<?php

namespace GraphQL\Resolvers;

use Models\Category;

class CategoryResolver
{
    /**
     * Resolve and return all categories from the database.
     *
     * @return array
     */
    public static function resolveCategories(): array
    {
        try {
            $category = new Category();
            return $category->fetchAll(); // Fetch all categories from the database
        } catch (\Exception $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Resolve products for a specific category.
     *
     * @param object $category
     * @return array
     */
    public static function resolveCategoryProducts($category): array
    {
        try {
            return $category->products(); // Fetch all products in the specific category
        } catch (\Exception $e) {
            error_log("Error fetching products for category: " . $e->getMessage());
            return [];
        }
    }
}
?>
