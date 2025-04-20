<?php
namespace Models;

class Gallery extends BaseModel {
    private $id;
    private $url;
    private $productId;

    public function __construct($db, $data, $productId) {
        parent::__construct($db);
        $this->id = md5($data['url']);
        $this->url = $data['url'];
        $this->productId = $productId;
    }

    public function save() {
        $query = "INSERT INTO gallery (gallery_id, url, products_id)
                  VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE url=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $this->id, $this->url, $this->productId, $this->url);

        $stmt->execute();
    }
}
?>