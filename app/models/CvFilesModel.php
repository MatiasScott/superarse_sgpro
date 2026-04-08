<?php
// app/models/CvFilesModel.php

require_once __DIR__ . '/BaseModel.php';

class CvFilesModel extends BaseModel
{
    protected $table = "cv_files";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $filePath,
        $fileType,
        $relatedTable = null,
        $relatedId = null
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, file_path, file_type, related_table, related_id
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $filePath);
        $stmt->bindParam(3, $fileType);
        $stmt->bindParam(4, $relatedTable);
        $stmt->bindParam(5, $relatedId);

        return $stmt->execute();
    }
}