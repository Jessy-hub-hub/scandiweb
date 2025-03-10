<?php
namespace Models;

class Price extends BaseModel {
    private $amount;
    private $currency_label;
    private $typename;

    public function __construct($db, $data) {
        parent::__construct($db);
        $this->amount = $data['amount'] ?? null;
        $this->currency_label = $data['currency']['label'] ?? null;
        $this->typename = $data['__typename'] ?? null;
    }

    public function save() {
        $query = "INSERT INTO prices (prices_amount, currency_label, __typename)
                  VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE __typename=?";

        $stmt = $this->db->prepare($query);

        if ($stmt) {
            $stmt->bind_param(
                "ssss",
                $this->amount, $this->currency_label, $this->typename, $this->typename
            );

            $stmt->execute();
            $stmt->close();
        } else {
            throw new \Exception("Failed to prepare statement for prices table.");
        }
    }
}
?>
