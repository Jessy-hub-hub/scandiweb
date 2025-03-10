<?php
namespace Models;

class Category extends BaseModel {
    private $name;
    private $typename;

    public function __construct($db, $data) {
        parent::__construct($db);
        $this->name = $data['name'] ?? null;
        $this->typename = $data['__typename'] ?? null;
    }

    public function save() {
        $query = "INSERT INTO categories (categories_name, __typename)
                  VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE __typename=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $this->name, $this->typename, $this->typename);

        $stmt->execute();
    }
}
?>