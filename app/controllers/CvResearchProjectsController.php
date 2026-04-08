<?php
// app/controllers/CvResearchProjectsController.php

require_once __DIR__ . '/../models/CvResearchProjectsModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvResearchProjectsController
{
    private $cvResearchProjectsModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvResearchProjectsModel = new CvResearchProjectsModel();
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
        $researchProjectsList = $this->cvResearchProjectsModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Proyectos de Investigación';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $denomination = $_POST['denomination'] ?? '';
            $scope = $_POST['scope'] ?? '';
            $responsibility = $_POST['responsibility'] ?? '';
            $entityName = $_POST['entity_name'] ?? '';
            $year = $_POST['year'] ?? '';
            $duration = $_POST['duration'] ?? '';

            if ($this->cvResearchProjectsModel->create(
                $professorId,
                $denomination,
                $scope,
                $responsibility,
                $entityName,
                $year,
                $duration
            )) {
                $lastId = $this->cvResearchProjectsModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_research_projects', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar el proyecto de investigación.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $denomination = $_POST['denomination'] ?? '';
            $scope = $_POST['scope'] ?? '';
            $responsibility = $_POST['responsibility'] ?? '';
            $entityName = $_POST['entity_name'] ?? '';
            $year = $_POST['year'] ?? '';
            $duration = $_POST['duration'] ?? '';

            if ($this->cvResearchProjectsModel->update(
                $id,
                $denomination,
                $scope,
                $responsibility,
                $entityName,
                $year,
                $duration
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_research_projects', $id);
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

        if ($this->cvResearchProjectsModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_research_projects', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}