<?php
// Include Composer's autoloader
require_once 'C:/xampp/htdocs/backend/vendor/autoload.php';

// Directly use the database connection class
use Config\Database;

// Initialize database connection if needed
$dbConnection = Database::connect();
?>