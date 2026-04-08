<?php
// app/models/CvTrainingModel.php

require_once __DIR__ . '/BaseModel.php';

class CvTrainingModel extends BaseModel
{
    protected $table = "cv_training";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $eventName,
        $institutionName,
        $year,
        $eventType,
        $durationHours
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, event_name, institution_name, year, event_type, duration_hours
        ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $eventName);
        $stmt->bindParam(3, $institutionName);
        $stmt->bindParam(4, $year);
        $stmt->bindParam(5, $eventType);
        $stmt->bindParam(6, $durationHours);

        return $stmt->execute();
    }
}