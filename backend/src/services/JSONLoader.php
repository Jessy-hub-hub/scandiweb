<?php
namespace Services;

use Exception;

class JSONLoader {
    public static function loadFromFile($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("JSON file not found: $filePath");
        }

        $jsonContent = file_get_contents($filePath);

        if (!$jsonContent) {
            throw new Exception("Failed to read JSON file: $filePath");
        }

        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }

        return $data; // Return parsed JSON cleanly
    }
}
?>