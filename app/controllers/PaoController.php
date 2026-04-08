<?php
// app/controllers/PaoController.php

require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php'; // Agregamos el modelo de auditoría
require_once __DIR__ . '/../helpers/ActivityHelper.php'; // Agregamos el helper de actividades
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';

class PaoController
{
    private $paoModel;
    private $userModel;
    private $roleModel;
    private $auditLogModel; // Propiedad para el modelo de auditoría

    public function __construct()
    {
        $this->paoModel = new PaoModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel(); // Instanciamos el modelo de auditoría
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);

        // Verificar que sea Super Administrador o Director de docencia
        $roleNames = array_column($roles, 'role_name');
        $hasAccess = in_array('Super Administrador', $roleNames) ||
            in_array('Director de docencia', $roleNames);

        // Si no tiene acceso, denegar
        if (!$hasAccess) {
            http_response_code(403);
            die("Acceso denegado: Solo Super Administrador y Director de docencia pueden acceder a este módulo.");
        }

        $paos = $this->paoModel->getAll();
        $pageTitle = 'Gestión de PAO';
        require_once __DIR__ . '/../views/pao/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);

        // Verificar que sea Super Administrador o Director de docencia
        $roleNames = array_column($roles, 'role_name');
        $hasAccess = in_array('Super Administrador', $roleNames) ||
            in_array('Director de docencia', $roleNames);

        // Si no tiene acceso, denegar
        if (!$hasAccess) {
            http_response_code(403);
            die("Acceso denegado: Solo Super Administrador y Director de docencia pueden acceder a este módulo.");
        }

        $pageTitle = 'Crear PAO';
        require_once __DIR__ . '/../views/pao/create.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        // Verificar que sea Super Administrador o Director de docencia
        $sessionRoles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($sessionRoles, 'role_name');
        $hasAccess = in_array('Super Administrador', $roleNames) ||
            in_array('Director de docencia', $roleNames);

        // Si no tiene acceso, denegar
        if (!$hasAccess) {
            http_response_code(403);
            die("Acceso denegado: Solo Super Administrador y Director de docencia pueden acceder a este módulo.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';

            if ($this->paoModel->create($name, $start_date, $end_date)) {

                // Obtener el ID del último PAO creado
                $lastPaoId = $this->paoModel->getLastInsertedId();

                // Lógica de Auditoría: Registrar la acción
                $userId = $_SESSION['user_id'] ?? null;
                $newData = ['name' => $name, 'start_date' => $start_date, 'end_date' => $end_date];
                $this->auditLogModel->logAction($userId, 'CREATE', 'pao', $lastPaoId, null, $newData);

                // Registrar actividad en el log de actividades
                ActivityHelper::logPaoCreate($lastPaoId, $name);

                // Crear notificación
                NotificationHelper::notifyPaoCreate($lastPaoId, $name);

                // Crear notificación
                NotificationHelper::notifyPaoCreate($lastPaoId, $name);

                header('Location: ' . BASE_PATH . '/pao');
                exit();
            } else {
                echo "Error al guardar el PAO.";
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

        // Verificar que sea Super Administrador o Director de docencia
        $roleNames = array_column($roles, 'role_name');
        $hasAccess = in_array('Super Administrador', $roleNames) ||
            in_array('Director de docencia', $roleNames);

        // Si no tiene acceso, denegar
        if (!$hasAccess) {
            http_response_code(403);
            die("Acceso denegado: Solo Super Administrador y Director de docencia pueden acceder a este módulo.");
        }

        $pao = $this->paoModel->find($id);

        if (!$pao) {
            header('Location: ' . BASE_PATH . '/pao');
            exit();
        }

        $pageTitle = 'Editar PAO';
        require_once __DIR__ . '/../views/pao/edit.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        // Verificar que sea Super Administrador o Director de docencia
        $sessionRoles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($sessionRoles, 'role_name');
        $hasAccess = in_array('Super Administrador', $roleNames) ||
            in_array('Director de docencia', $roleNames);

        // Si no tiene acceso, denegar
        if (!$hasAccess) {
            http_response_code(403);
            die("Acceso denegado: Solo Super Administrador y Director de docencia pueden acceder a este módulo.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';

            $oldPao = $this->paoModel->find($id);

            if ($this->paoModel->update($id, $name, $start_date, $end_date)) {
                $userId = $_SESSION['user_id'] ?? null;
                $oldData = $oldPao;
                $newData = ['name' => $name, 'start_date' => $start_date, 'end_date' => $end_date];
                $this->auditLogModel->logAction($userId, 'UPDATE', 'pao', $id, $oldData, $newData);

                ActivityHelper::logPaoUpdate($id, $name);
                NotificationHelper::notifyPaoUpdate($id, $name);

                header('Location: ' . BASE_PATH . '/pao');
                exit();
            } else {
                echo "Error al actualizar el PAO.";
            }
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        // Verificar que sea Super Administrador o Director de docencia
        $sessionRoles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($sessionRoles, 'role_name');
        $hasAccess = in_array('Super Administrador', $roleNames) ||
            in_array('Director de docencia', $roleNames);

        // Si no tiene acceso, denegar
        if (!$hasAccess) {
            http_response_code(403);
            die("Acceso denegado: Solo Super Administrador y Director de docencia pueden acceder a este módulo.");
        }

        $pao = $this->paoModel->find($id);
        if ($pao) {
            $this->paoModel->delete($id);

            $userId = $_SESSION['user_id'] ?? null;
            $this->auditLogModel->logAction($userId, 'DELETE', 'pao', $id, $pao, null);

            ActivityHelper::logPaoDelete($id, $pao['title']);
            NotificationHelper::notifyPaoDelete($id, $pao['title']);
        }

        header('Location: ' . BASE_PATH . '/pao');
        exit();
    }
}
