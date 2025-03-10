<?php
namespace Models;

class Item extends BaseModel {
    private $id;
    private $displayValue;
    private $value;
    private $typename;

    public function __construct($db, $data) {
        parent::__construct($db);
        $this->id = $data['id'] ?? null;
        $this->displayValue = $data['displayValue'] ?? null;
        $this->value = $data['value'] ?? null;
        $this->typename = $data['__typename'] ?? null;
    }

    public function save() {
        $query = "INSERT INTO items (items_id, displayValue, value, __typename)
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE displayValue=?, value=?, __typename=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssssss",
            $this->id, $this->displayValue, $this->value, $this->typename,
            $this->displayValue, $this->value, $this->typename
        );

        $stmt->execute();
    }
}
?>