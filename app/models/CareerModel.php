<?php
require_once __DIR__ . '/BaseModel.php';

class CareerModel extends BaseModel {
    protected $table = "careers";

    public function __construct() {
        parent::__construct();
    }

    public function create($name, $code = null, $description = null) {
        $query = "INSERT INTO " . $this->table . " (name, code, description) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $code = $code !== null ? htmlspecialchars(strip_tags($code)) : null;
        $description = $description !== null ? trim($description) : null;
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $code);
        $stmt->bindParam(3, $description);
        return $stmt->execute();
    }

    public function update($id, $name, $code = null, $description = null) {
        $query = "UPDATE " . $this->table . " SET name = ?, code = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $code = $code !== null ? htmlspecialchars(strip_tags($code)) : null;
        $description = $description !== null ? trim($description) : null;
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $code);
        $stmt->bindParam(3, $description);
        $stmt->bindParam(4, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}