<?php
require_once __DIR__ . '/BaseModel.php';

class CareerModel extends BaseModel {
    protected $table = "careers";

    public function __construct() {
        parent::__construct();
    }

    public function create($name) {
        $query = "INSERT INTO " . $this->table . " (name) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(1, $name);
        return $stmt->execute();
    }

    public function update($id, $name) {
        $query = "UPDATE " . $this->table . " SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $id);
        return $stmt->execute();
    }
}