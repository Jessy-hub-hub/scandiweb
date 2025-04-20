<?php
namespace Config;

use mysqli;

class Database
{
    // Make connect() static
    public static function connect()
    {
        $dbConfig = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'scandiweb'
        ];
        
        $db = new mysqli(
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database']
        );

        if ($db->connect_error) {
            throw new \Exception('Database connection failed: ' . $db->connect_error);
        }

        return $db;
    }
}
?>