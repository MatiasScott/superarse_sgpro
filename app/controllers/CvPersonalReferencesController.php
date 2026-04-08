<?php
// app/controllers/CvPersonalReferencesController.php

require_once __DIR__ . '/../models/CvPersonalReferencesModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvPersonalReferencesController
{
    private $cvPersonalReferencesModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvPersonalReferencesModel = new CvPersonalReferencesModel();
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
        $personalReferencesList = $this->cvPersonalReferencesModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Referencias Personales';
        require_once __DIR__ . '/../views/professor/cv/index.php#subsection-personales';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $contactPerson = $_POST['contact_person'] ?? '';
            $relationshipType = $_POST['relationship_type'] ?? '';
            $contactNumber = $_POST['contact_number'] ?? '';

            if ($this->cvPersonalReferencesModel->create(
                $professorId,
                $contactPerson,
                $relationshipType,
                $contactNumber
            )) {
                $lastId = $this->cvPersonalReferencesModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_personal_references', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la referencia personal.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $contactPerson = $_POST['contact_person'] ?? '';
            $relationshipType = $_POST['relationship_type'] ?? '';
            $contactNumber = $_POST['contact_number'] ?? '';

            if ($this->cvPersonalReferencesModel->update(
                $id,
                $contactPerson,
                $relationshipType,
                $contactNumber
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_personal_references', $id);
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

        if ($this->cvPersonalReferencesModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_personal_references', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}