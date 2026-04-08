<?php
// app/controllers/CvTrainingController.php

require_once __DIR__ . '/../models/CvTrainingModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvTrainingController
{
    private $cvTrainingModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvTrainingModel = new CvTrainingModel();
        $this->auditLogModel = new AuditLogModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $trainingList = $this->cvTrainingModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Capacitación Profesional';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $eventName = $_POST['event_name'] ?? '';
            $institutionName = $_POST['institution_name'] ?? '';
            $year = $_POST['year'] ?? '';
            $eventType = $_POST['event_type'] ?? '';
            $durationHours = $_POST['duration_hours'] ?? '';

            if ($this->cvTrainingModel->create(
                $professorId,
                $eventName,
                $institutionName,
                $year,
                $eventType,
                $durationHours
            )) {
                $lastId = $this->cvTrainingModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_training', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la información de capacitación.";
            }
        }
    }
}