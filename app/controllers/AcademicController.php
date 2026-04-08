<?php
// app/controllers/AcademicController.php

require_once __DIR__ . '/../models/CareerModel.php';
require_once __DIR__ . '/../models/SubjectModel.php';
require_once __DIR__ . '/../models/ProfessorAssignmentModel.php';
require_once __DIR__ . '/../models/UserModel.php'; // Se agregó
require_once __DIR__ . '/../models/RoleModel.php'; // Se agregó

class AcademicController
{
    private $careerModel;
    private $subjectModel;
    private $assignmentModel;
    private $userModel;
    private $roleModel;

    public function __construct()
    {
        $this->careerModel = new CareerModel();
        $this->subjectModel = new SubjectModel();
        $this->assignmentModel = new ProfessorAssignmentModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function careers()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);

        $careers = $this->careerModel->getAll();
        $pageTitle = 'Gestión de Carreras';
        require_once __DIR__ . '/../views/academic/careers.php';
    }
    
    public function subjects()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        $subjects = $this->subjectModel->getAll();
        $pageTitle = 'Gestión de Asignaturas';
        require_once __DIR__ . '/../views/academic/subjects.php';
    }
    
    public function assignments()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        $assignments = $this->assignmentModel->getAll();
        $pageTitle = 'Asignaciones a Profesores';
        require_once __DIR__ . '/../views/academic/assignments.php';
    }
}