<?php

namespace Models;

use Exception;

class Product extends BaseModel
{
    private $id;
    private $name;
    private $inStock;
    private $description;
    private $category;
    private $pricesAmount;
    private $brand;
    private $typename;

    /**
     * Constructor to initialize product data
     */
    public function __construct($db, array $data)
    {
        parent::__construct($db);

        // Map input data to the class properties
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->inStock = isset($data['inStock']) ? intval($data['inStock']) : 0;
        $this->description = $data['description'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->pricesAmount = $data['prices_amount'] ?? '0';
        $this->brand = $data['brand'] ?? '';
        $this->typename = $data['__typename'] ?? '';
    }

    /**
     * Save product data into the database
     */
    public function save()
    {
        $query = "
            INSERT INTO products (products_id, name, inStock, description, category, prices_amount, brand, __typename)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                name = VALUES(name),
                inStock = VALUES(inStock),
                description = VALUES(description),
                category = VALUES(category),
                prices_amount = VALUES(prices_amount),
                brand = VALUES(brand),
                __typename = VALUES(__typename)
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new Exception("Statement preparation failed: " . $this->db->error);
        }

        // Bind parameters securely to prevent SQL Injection
        $stmt->bind_param(
            "ssisssss",
            $this->id,
            $this->name,
            $this->inStock,
            $this->description,
            $this->category,
            $this->pricesAmount,
            $this->brand,
            $this->typename
        );

        // Execute query and handle errors
        if (!$stmt->execute()) {
            throw new Exception("Statement execution error: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>