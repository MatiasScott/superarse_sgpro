<?php
// app/models/CvEducationModel.php

require_once __DIR__ . '/BaseModel.php';

class CvEducationModel extends BaseModel
{
    protected $table = "cv_education";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $educationLevel,
        $institutionName,
        $degreeTitle,
        $senescytRegister
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, education_level, institution_name, degree_title, senescyt_register
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $educationLevel);
        $stmt->bindParam(3, $institutionName);
        $stmt->bindParam(4, $degreeTitle);
        $stmt->bindParam(5, $senescytRegister);

        return $stmt->execute();
    }

    public function findById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(
        $id,
        $educationLevel,
        $institutionName,
        $degreeTitle,
        $senescytRegister
    ) {
        $query = "UPDATE {$this->table} SET 
            education_level = ?,
            institution_name = ?,
            degree_title = ?,
            senescyt_register = ?
        WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $educationLevel);
        $stmt->bindParam(2, $institutionName);
        $stmt->bindParam(3, $degreeTitle);
        $stmt->bindParam(4, $senescytRegister);
        $stmt->bindParam(5, $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}