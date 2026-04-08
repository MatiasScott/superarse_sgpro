<?php
// app/models/ProfessorAssignmentModel.php
require_once __DIR__ . '/BaseModel.php';

class ProfessorAssignmentModel extends BaseModel {
    protected $table = 'professor_assignments';

    public function __construct() {
        parent::__construct();
    }
}