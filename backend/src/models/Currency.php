<?php
namespace Models;

class Currency {
    private $db;
    private $data;

    /**
     * Constructor for the Currency model.
     *
     * @param \PDO $db The database connection.
     * @param array $data Associative array containing currency data.
     */
    public function __construct($db, array $data) {
        $this->db = $db;
        $this->data = $data;
    }

    /**
     * Save the currency data into the `currency` table.
     *
     * @throws \Exception If the query fails.
     */
    public function save() {
        $query = "INSERT IGNORE INTO currency (currency_label, symbol, __typename) 
                  VALUES (:label, :symbol, :typename)";

        $stmt = $this->db->prepare($query);

        // Execute the query with bound parameters
        $stmt->execute([
            ':label' => $this->data['label'],
            ':symbol' => $this->data['symbol'],
            ':typename' => $this->data['__typename'],
        ]);
    }
}
?>