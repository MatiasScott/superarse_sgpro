<?php
// app/controllers/CvTeachingExperienceController.php

require_once __DIR__ . '/../models/CvTeachingExperienceModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvTeachingExperienceController
{
    private $cvTeachingExperienceModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvTeachingExperienceModel = new CvTeachingExperienceModel();
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
        $teachingExperienceList = $this->cvTeachingExperienceModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Experiencia en Docencia';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $ies = $_POST['ies'] ?? '';
            $denomination = $_POST['denomination'] ?? '';
            $subjects = $_POST['subjects'] ?? '';

            if ($this->cvTeachingExperienceModel->create(
                $professorId,
                $startDate,
                $endDate,
                $ies,
                $denomination,
                $subjects
            )) {
                $lastId = $this->cvTeachingExperienceModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_teaching_experience', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la experiencia en docencia.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $ies = $_POST['ies'] ?? '';
            $denomination = $_POST['denomination'] ?? '';
            $subjects = $_POST['subjects'] ?? '';

            if ($this->cvTeachingExperienceModel->update(
                $id,
                $startDate,
                $endDate,
                $ies,
                $denomination,
                $subjects
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_teaching_experience', $id);
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
        exit();
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $professorId = $_SESSION['user_id'];

        if ($this->cvTeachingExperienceModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_teaching_experience', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}