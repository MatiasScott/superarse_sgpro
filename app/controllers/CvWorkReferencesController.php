<?php
// app/controllers/CvWorkReferencesController.php

require_once __DIR__ . '/../models/CvWorkReferencesModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvWorkReferencesController
{
    private $cvWorkReferencesModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvWorkReferencesModel = new CvWorkReferencesModel();
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
        $workReferencesList = $this->cvWorkReferencesModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Referencias Laborales';
        require_once __DIR__ . '/../views/professor/cv/index.php#subsection-laborales';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $contactPerson = $_POST['contact_person'] ?? '';
            $relationPosition = $_POST['relation_position'] ?? '';
            $organizationCompany = $_POST['organization_company'] ?? '';
            $contactNumber = $_POST['contact_number'] ?? '';

            if ($this->cvWorkReferencesModel->create(
                $professorId,
                $contactPerson,
                $relationPosition,
                $organizationCompany,
                $contactNumber
            )) {
                $lastId = $this->cvWorkReferencesModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_work_references', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la referencia laboral.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $contactPerson = $_POST['contact_person'] ?? '';
            $relationPosition = $_POST['relation_position'] ?? '';
            $organizationCompany = $_POST['organization_company'] ?? '';
            $contactNumber = $_POST['contact_number'] ?? '';

            if ($this->cvWorkReferencesModel->update(
                $id,
                $contactPerson,
                $relationPosition,
                $organizationCompany,
                $contactNumber
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_work_references', $id);
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

        if ($this->cvWorkReferencesModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_work_references', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}