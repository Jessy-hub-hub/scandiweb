<?php
namespace Models;

class DataTable extends BaseModel {
    private $productId;
    private $categoryName;

    public function __construct($db, $data) {
        parent::__construct($db);
        $this->productId = $data['products_id'] ?? null;
        $this->categoryName = $data['categories_name'] ?? null;
    }

    public function save() {
        $query = "INSERT INTO data (products_id, categories_name)
                  VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE categories_name=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $this->productId, $this->categoryName, $this->categoryName);

        $stmt->execute();
    }
}
?>