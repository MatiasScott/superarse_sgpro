<?php
// app/controllers/CvLanguageProficiencyController.php

require_once __DIR__ . '/../models/CvLanguageProficiencyModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvLanguageProficiencyController
{
    private $cvLanguageProficiencyModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvLanguageProficiencyModel = new CvLanguageProficiencyModel();
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
        $languageList = $this->cvLanguageProficiencyModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Dominio de Idiomas';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $language = $_POST['language'] ?? '';
            $certificateName = $_POST['certificate_name'] ?? '';
            $institutionName = $_POST['institution_name'] ?? '';

            if ($this->cvLanguageProficiencyModel->create(
                $professorId,
                $language,
                $certificateName,
                $institutionName
            )) {
                $lastId = $this->cvLanguageProficiencyModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_language_proficiency', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la información de idiomas.";
            }
        }
    }
}