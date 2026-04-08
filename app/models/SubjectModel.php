<?php
// app/models/SubjectModel.php

require_once __DIR__ . '/BaseModel.php';

class SubjectModel extends BaseModel
{
    protected $table = "subjects";

    public function __construct()
    {
        parent::__construct();
    }

    // Método para obtener todas las asignaturas con el nombre de su carrera
    public function getSubjectsWithCareerNames()
    {
        $query = "SELECT s.id, s.name, c.name AS career_name 
                  FROM subjects s
                  JOIN careers c ON s.career_id = c.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $careerId)
    {
        $query = "INSERT INTO " . $this->table . " (name, career_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $careerId);
        return $stmt->execute();
    }

    public function update($id, $name, $careerId)
    {
        $query = "UPDATE " . $this->table . " SET name = ?, career_id = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $careerId);
        $stmt->bindParam(3, $id);
        return $stmt->execute();
    }

    // Método para obtener una sola asignatura, incluyendo el nombre de la carrera
    public function findWithCareer($id)
    {
        $query = "SELECT s.id, s.name, s.career_id, c.name AS career_name
                  FROM subjects s
                  JOIN careers c ON s.career_id = c.id
                  WHERE s.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
