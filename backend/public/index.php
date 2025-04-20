<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/controllers/GraphQL.php';

// Import database-related services
use Config\Database;
use Services\JSONLoader;
use Services\DatabaseSeeder;
use App\Controller\GraphQL;

// Define allowed origins
$allowedOrigins = [
    'http://localhost:3000',
    'https://jessy-hub-hub.github.io'
];

// Dynamically set CORS headers based on the request's Origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: " . $origin);
    } else {
        header("Access-Control-Allow-Origin: https://jessy-hub-hub.github.io");
    }
} else {
    header("Access-Control-Allow-Origin: https://jessy-hub-hub.github.io");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

// FastRoute setup for GraphQL endpoint
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/graphql', [GraphQL::class, 'handle']);
    $r->addRoute('GET', '/graphql', [GraphQL::class, 'handle']);
});

// Dispatch the request based on the method and URI
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Instead of returning 404 for non-GraphQL routes,
        // serve the index.html file to let BrowserRouter handle routing
        $indexFile = __DIR__ . '/../public/index.html'; // adjust path as necessary
        if (file_exists($indexFile)) {
            header("Content-Type: text/html; charset=UTF-8");
            readfile($indexFile);
            exit();
        } else {
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['error' => 'Not Found']);
            exit();
        }
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header('HTTP/1.0 405 Method Not Allowed');
        echo json_encode(['error' => 'Method Not Allowed']);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Optional: Database seeding for POST requests with 'seed' parameter
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['seed'])) {
            try {
                $db = (new Database())->connect();
                $json = JSONLoader::loadFromFile(__DIR__ . '/../src/services/data/data.json');
                $seeder = new DatabaseSeeder($db);
                $seeder->seed($json);
                echo json_encode(['message' => 'Database seeding completed successfully!']);
                exit;
            } catch (\Exception $e) {
                echo json_encode(['error' => 'Error during database seeding: ' . $e->getMessage()]);
                exit;
            }
        }

        // Handle the GraphQL request
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
            $query = $_GET['query'];
            echo $handler($vars, $query);
        } else {
            echo $handler($vars);
        }
        break;
}
?>
