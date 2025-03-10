<?php

use GraphQL\Server\StandardGraphQLServer;
use GraphQL\GraphQL;
use GraphQL\Resolvers\ProductResolver;
use GraphQL\Resolvers\CategoryResolver;
use GraphQL\Resolvers\OrderResolver;

// Register resolvers
$resolvers = [
    'Product' => ProductResolver::class,
    'Category' => CategoryResolver::class,
    'Order' => OrderResolver::class,
];

// Create GraphQL Server instance
$server = new StandardGraphQLServer([
    'schema' => require_once __DIR__ . '/../graphql/schema.graphql',
    'resolvers' => $resolvers
]);

return $server;
?>