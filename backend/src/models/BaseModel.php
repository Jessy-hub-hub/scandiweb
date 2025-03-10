<?php
namespace Models;

// Base model that all models will extend
abstract class BaseModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    abstract public function save();
}
?>