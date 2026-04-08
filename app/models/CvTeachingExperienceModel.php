<?php
// app/models/CvTeachingExperienceModel.php

require_once __DIR__ . '/BaseModel.php';

class CvTeachingExperienceModel extends BaseModel
{
    protected $table = "cv_teaching_experience";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $startDate,
        $endDate,
        $ies,
        $denomination,
        $subjects
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, start_date, end_date, ies, denomination, subjects
        ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $startDate);
        $stmt->bindParam(3, $endDate);
        $stmt->bindParam(4, $ies);
        $stmt->bindParam(5, $denomination);
        $stmt->bindParam(6, $subjects);

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
        $startDate,
        $endDate,
        $ies,
        $denomination,
        $subjects
    ) {
        $query = "UPDATE {$this->table} SET 
            start_date = ?,
            end_date = ?,
            ies = ?,
            denomination = ?,
            subjects = ?
        WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $startDate);
        $stmt->bindParam(2, $endDate);
        $stmt->bindParam(3, $ies);
        $stmt->bindParam(4, $denomination);
        $stmt->bindParam(5, $subjects);
        $stmt->bindParam(6, $id);

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