<?php
namespace App\GraphQL\Resolvers;

use Models\AbstractAttribute;

class AttributeResolver {
    public static function resolveAttributes($product): array {
        return $product->getAttributes();
    }
}
?>