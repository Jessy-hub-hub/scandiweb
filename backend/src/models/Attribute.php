<?php
namespace Models;

class Attribute extends BaseModel {
    private $id;
    private $name;
    private $type;
    private $typename;
    private $productId;

    public function __construct($db, $data, $productId) {
        parent::__construct($db);
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->typename = $data['__typename'] ?? null;
        $this->productId = $productId;
    }

    public function save() {
        $query = "INSERT INTO attributes (attributes_id, name, type, __typename, products_id)
                  VALUES (?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE name=?, type=?, __typename=?, products_id=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "sssss",
            $this->id, $this->name, $this->type, $this->typename, $this->productId,
            $this->name, $this->type, $this->typename, $this->productId
        );

        $stmt->execute();
    }
}
?>