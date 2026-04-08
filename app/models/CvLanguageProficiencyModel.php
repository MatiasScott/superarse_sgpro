<?php
// app/models/CvLanguageProficiencyModel.php

require_once __DIR__ . '/BaseModel.php';

class CvLanguageProficiencyModel extends BaseModel
{
    protected $table = "cv_language_proficiency";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $language,
        $certificateName,
        $institutionName
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, language, certificate_name, institution_name
        ) VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $language);
        $stmt->bindParam(3, $certificateName);
        $stmt->bindParam(4, $institutionName);

        return $stmt->execute();
    }
}