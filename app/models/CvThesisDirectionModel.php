<?php
// app/models/CvThesisDirectionModel.php

require_once __DIR__ . '/BaseModel.php';

class CvThesisDirectionModel extends BaseModel
{
    protected $table = "cv_thesis_direction";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $studentName,
        $thesisTitle,
        $academicProgram,
        $universityName
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, student_name, thesis_title, academic_program, university_name
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $studentName);
        $stmt->bindParam(3, $thesisTitle);
        $stmt->bindParam(4, $academicProgram);
        $stmt->bindParam(5, $universityName);

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
        $studentName,
        $thesisTitle,
        $academicProgram,
        $universityName
    ) {
        $query = "UPDATE {$this->table} SET 
            student_name = ?,
            thesis_title = ?,
            academic_program = ?,
            university_name = ?
        WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $studentName);
        $stmt->bindParam(2, $thesisTitle);
        $stmt->bindParam(3, $academicProgram);
        $stmt->bindParam(4, $universityName);
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