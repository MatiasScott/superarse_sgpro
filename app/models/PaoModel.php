<?php
// app/models/PaoModel.php

require_once __DIR__ . '/BaseModel.php';

class PaoModel extends BaseModel
{
    protected $table = "pao";

    public function __construct()
    {
        parent::__construct();
    }


    public function create($title, $start_date, $end_date)
    {
        $query = "INSERT INTO " . $this->table . " (title, start_date, end_date) VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($query);

        // Limpieza y asignación de datos
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $start_date);
        $stmt->bindParam(3, $end_date);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getLastInsertedId()
    {
        return $this->db->lastInsertId();
    }

    public function update($id, $title, $start_date, $end_date)
    {
        $query = "UPDATE " . $this->table . " SET title = ?, start_date = ?, end_date = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $start_date);
        $stmt->bindParam(3, $end_date);
        $stmt->bindParam(4, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
