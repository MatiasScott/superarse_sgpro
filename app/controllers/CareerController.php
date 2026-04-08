<?php
require_once __DIR__ . '/../models/CareerModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';

class CareerController
{
    private $careerModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->careerModel = new CareerModel();
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        // Verificar que NO sea solo profesor
        $roleNames = array_column($roles, 'role_name');
        $isOnlyProfessor = in_array('Profesor', $roleNames) && 
                          !in_array('Coordinador académico', $roleNames) && 
                          !in_array('Director de docencia', $roleNames) && 
                          !in_array('Super Administrador', $roleNames) &&
                          !in_array('Talento humano', $roleNames);
        
        // Si es solo profesor, denegar acceso
        if ($isOnlyProfessor) {
            http_response_code(403);
            die("Acceso denegado: No tiene permisos para acceder a este módulo.");
        }
        
        $careers = $this->careerModel->getAll();
        $pageTitle = 'Gestión de Carreras';
        require_once __DIR__ . '/../views/academic/careers.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        // Verificar que NO sea solo profesor
        $roleNames = array_column($roles, 'role_name');
        $isOnlyProfessor = in_array('Profesor', $roleNames) && 
                          !in_array('Coordinador académico', $roleNames) && 
                          !in_array('Director de docencia', $roleNames) && 
                          !in_array('Super Administrador', $roleNames) &&
                          !in_array('Talento humano', $roleNames);
        
        // Si es solo profesor, denegar acceso
        if ($isOnlyProfessor) {
            http_response_code(403);
            die("Acceso denegado: No tiene permisos para acceder a este módulo.");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';

            if ($this->careerModel->create($name)) {
                $lastCareerId = $this->careerModel->getLastInsertedId();
                $userId = $_SESSION['user_id'] ?? null;
                $newData = ['name' => $name];
                $this->auditLogModel->logAction($userId, 'CREATE', 'careers', $lastCareerId, null, $newData);

                header('Location: ' . BASE_PATH . '/academic/careers');
                exit();
            } else {
                echo "Error al guardar la carrera.";
            }
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        // Verificar que NO sea solo profesor
        $roleNames = array_column($roles, 'role_name');
        $isOnlyProfessor = in_array('Profesor', $roleNames) && 
                          !in_array('Coordinador académico', $roleNames) && 
                          !in_array('Director de docencia', $roleNames) && 
                          !in_array('Super Administrador', $roleNames) &&
                          !in_array('Talento humano', $roleNames);
        
        // Si es solo profesor, denegar acceso
        if ($isOnlyProfessor) {
            http_response_code(403);
            die("Acceso denegado: No tiene permisos para acceder a este módulo.");
        }
        
        $career = $this->careerModel->find($id);

        if (!$career) {
            header('Location: ' . BASE_PATH . '/academic/careers');
            exit();
        }

        $pageTitle = 'Editar Carrera: ' . htmlspecialchars($career['name']);
        require_once __DIR__ . '/../views/academic/edit-career.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        // Verificar que NO sea solo profesor
        $roleNames = array_column($roles, 'role_name');
        $isOnlyProfessor = in_array('Profesor', $roleNames) && 
                          !in_array('Coordinador académico', $roleNames) && 
                          !in_array('Director de docencia', $roleNames) && 
                          !in_array('Super Administrador', $roleNames) &&
                          !in_array('Talento humano', $roleNames);
        
        // Si es solo profesor, denegar acceso
        if ($isOnlyProfessor) {
            http_response_code(403);
            die("Acceso denegado: No tiene permisos para acceder a este módulo.");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';

            $oldCareer = $this->careerModel->find($id);

            if ($this->careerModel->update($id, $name)) {
                $userId = $_SESSION['user_id'] ?? null;
                $newData = ['name' => $name];
                $oldData = ['name' => $oldCareer['name']];
                $this->auditLogModel->logAction($userId, 'UPDATE', 'careers', $id, $oldData, $newData);

                header('Location: ' . BASE_PATH . '/academic/careers');
                exit();
            } else {
                echo "Error al actualizar la carrera.";
            }
        }
    }

    public function quickStore()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';

            if ($this->careerModel->create($name)) {
                $lastCareerId = $this->careerModel->getLastInsertedId();
                echo json_encode(['success' => true, 'id' => $lastCareerId, 'name' => $name]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar la carrera.']);
                exit();
            }
        }
    }
}
