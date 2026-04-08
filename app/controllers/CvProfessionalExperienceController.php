<?php
// app/controllers/CvProfessionalExperienceController.php

require_once __DIR__ . '/../models/CvProfessionalExperienceModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvProfessionalExperienceController
{
    private $cvProfessionalExperienceModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvProfessionalExperienceModel = new CvProfessionalExperienceModel();
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
        $professionalExperienceList = $this->cvProfessionalExperienceModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Experiencia Profesional';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $companyName = $_POST['company_name'] ?? '';
            $position = $_POST['position'] ?? '';
            $activitiesDescription = $_POST['activities_description'] ?? '';

            if ($this->cvProfessionalExperienceModel->create(
                $professorId,
                $startDate,
                $endDate,
                $companyName,
                $position,
                $activitiesDescription
            )) {
                $lastId = $this->cvProfessionalExperienceModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_professional_experience', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la experiencia profesional.";
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
            $companyName = $_POST['company_name'] ?? '';
            $position = $_POST['position'] ?? '';
            $activitiesDescription = $_POST['activities_description'] ?? '';

            if ($this->cvProfessionalExperienceModel->update(
                $id,
                $startDate,
                $endDate,
                $companyName,
                $position,
                $activitiesDescription
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_professional_experience', $id);
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

        if ($this->cvProfessionalExperienceModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_professional_experience', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}