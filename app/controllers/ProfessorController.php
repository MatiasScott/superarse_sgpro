<?php
// app/controllers/ProfessorController.php

require_once __DIR__ . '/../models/ProfessorCvModel.php';
require_once __DIR__ . '/../models/CvEducationModel.php';
// ... y todos los demás modelos de CV que necesites
require_once __DIR__ . '/../models/UserModel.php'; // Agregamos el modelo de usuario
require_once __DIR__ . '/../models/RoleModel.php'; // Agregamos el modelo de rol

class ProfessorController
{
    private $professorCvModel;
    private $cvEducationModel;
    private $userModel; // Propiedad para el modelo de usuario
    private $roleModel; // Propiedad para el modelo de rol

    public function __construct()
    {
        $this->professorCvModel = new ProfessorCvModel();
        $this->cvEducationModel = new CvEducationModel();
        // ... instanciar los demás modelos de CV
        $this->userModel = new UserModel(); // Instanciamos el modelo de usuario
        $this->roleModel = new RoleModel(); // Instanciamos el modelo de rol
    }

    public function showProfile()
    {
        // Lógica para mostrar el perfil del profesor
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        
        // CORRECCIÓN: Obtener los roles del usuario actual para la barra de navegación.
        $user = $this->userModel->find($_SESSION['user_id']);
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        $professorId = $_SESSION['user_id'];
        $cvData = $this->professorCvModel->find($professorId);
        $educationData = $this->cvEducationModel->findByProfessorId($professorId);
        
        $pageTitle = 'Perfil del Profesor';
        require_once __DIR__ . '/../views/professor/profile.php';
    }

    // Método para guardar los datos de la hoja de vida
    public function saveCv()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lógica para procesar el formulario de CV
            // (Guardar datos en las tablas correspondientes)
            // Ejemplo:
            // $this->professorCvModel->update($professorId, $_POST['personal_info']);
            // $this->cvEducationModel->create($_POST['education_data']);
        }
    }
}