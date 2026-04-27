<?php
// app/controllers/UserController.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class UserController
{
    private $userModel;
    private $roleModel;
    private $auditLogModel; // Propiedad para el modelo de auditoría
    private const ESCUELA_OPTIONS = ['ECSOS', 'ECAVET', 'ECSET'];

    public function __construct()
    {
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
        PermissionHelper::enforce('users', 'view', $roles, '/dashboard');
        
        
        $users = $this->userModel->getAll();
        $pageTitle = 'Gestión de Usuarios';
        require_once __DIR__ . '/../views/users/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        // Obtener los roles del USUARIO EN SESIÓN para el menú
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        PermissionHelper::enforce('users', 'manage_all', $roles, '/users');
        
        
        // Obtener TODOS los roles disponibles para el formulario
        $allRoles = $this->roleModel->getAll();
        
        $pageTitle = 'Crear Nuevo Usuario';
        
        require_once __DIR__ . '/../views/users/create.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        
        $sessionRoles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('users', 'manage_all', $sessionRoles, '/users');
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role_id = $_POST['role_id'] ?? null;
            $dedication = trim($_POST['dedication'] ?? '');
            $escuela = $this->normalizeEscuela($_POST['escuela'] ?? null);

            if (isset($_POST['escuela']) && $_POST['escuela'] !== '' && $escuela === null) {
                $_SESSION['flash_error'] = 'La escuela seleccionada no es válida.';
                $_SESSION['old_user'] = ['name' => $name, 'email' => $email, 'role_id' => $role_id, 'dedication' => $dedication, 'escuela' => $_POST['escuela']];
                header('Location: ' . BASE_PATH . '/users/create');
                exit();
            }

            // Basic validation
            if (empty($name) || empty($email) || empty($password) || empty($role_id) || empty($dedication)) {
                $_SESSION['flash_error'] = 'Todos los campos son obligatorios.';
                $_SESSION['old_user'] = ['name' => $name, 'email' => $email, 'role_id' => $role_id, 'dedication' => $dedication, 'escuela' => $escuela];
                header('Location: ' . BASE_PATH . '/users/create');
                exit();
            }

            try {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Create user
                $created = $this->userModel->create($name, $email, $hashedPassword, $dedication, $escuela);
                if (!$created) {
                    throw new Exception('No se pudo crear el usuario en la base de datos');
                }

                $lastUserId = $this->userModel->getLastInsertedId();

                // Assign role
                if (!$this->roleModel->assignRoleToUser($lastUserId, $role_id)) {
                    // rollback: delete created user
                    $this->userModel->delete($lastUserId);
                    throw new Exception('No se pudo asignar el rol al usuario');
                }

                // Audit, activity and notification
                $userId = $_SESSION['user_id'] ?? null;
                $newData = ['name' => $name, 'email' => $email, 'role_id' => $role_id, 'dedication' => $dedication, 'escuela' => $escuela];
                $this->auditLogModel->logAction($userId, 'CREATE', 'users', $lastUserId, null, $newData);
                ActivityHelper::logUserCreate($lastUserId, $name);
                NotificationHelper::notifyUserCreate($lastUserId, $name);

                $_SESSION['flash_success'] = 'Usuario creado correctamente.';
                header('Location: ' . BASE_PATH . '/users');
                exit();
            } catch (Exception $e) {
                error_log('UserController::store error: ' . $e->getMessage());
                error_log($e->getTraceAsString());
                // Mostrar el mensaje de excepción en flash temporariamente para debugging
                $_SESSION['flash_error'] = 'Error al crear el usuario: ' . $e->getMessage();
                $_SESSION['old_user'] = ['name' => $name, 'email' => $email, 'role_id' => $role_id, 'dedication' => $dedication, 'escuela' => $escuela];
                header('Location: ' . BASE_PATH . '/users/create');
                exit();
            }
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        // Obtener los roles del USUARIO EN SESIÓN para el menú
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        PermissionHelper::enforce('users', 'manage_all', $roles, '/users');


        // Obtener el usuario que se va a editar
        $user = $this->userModel->find($id);
        if (!$user) {
            header('Location: ' . BASE_PATH . '/users');
            exit();
        }

        // Obtener todos los roles disponibles
        $allRoles = $this->roleModel->getAll();

        // Obtener los roles específicos del USUARIO QUE SE EDITA
        $userRoles = $this->roleModel->getRolesByUserId($id);

        $pageTitle = 'Editar Usuario: ' . htmlspecialchars($user['name']);

        require_once __DIR__ . '/../views/users/edit.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        
        $sessionRoles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('users', 'manage_all', $sessionRoles, '/users');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Obtener los datos del formulario
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $roles = $_POST['roles'] ?? []; // Los roles seleccionados son un array
            $dedication = trim($_POST['dedication'] ?? '');
            $escuela = $this->normalizeEscuela($_POST['escuela'] ?? null);

            if (isset($_POST['escuela']) && $_POST['escuela'] !== '' && $escuela === null) {
                $_SESSION['flash_error'] = 'La escuela seleccionada no es válida.';
                header('Location: ' . BASE_PATH . '/users/edit/' . $id);
                exit();
            }

            // Validación básica
            if (empty($name) || empty($email)) {
                $_SESSION['flash_error'] = 'El nombre y el correo son obligatorios.';
                header('Location: ' . BASE_PATH . '/users/edit/' . $id);
                exit();
            }

            try {
                // 1. Obtener los datos antiguos para la auditoría
                $oldUser = $this->userModel->find($id);
                if (!$oldUser) {
                    throw new Exception('Usuario no encontrado.');
                }
                $oldRoles = $this->roleModel->getRolesByUserId($id);

                // 2. Actualizar la información básica del usuario
                if (!$this->userModel->update($id, $name, $email, $dedication, $escuela)) {
                    throw new Exception('No se pudo actualizar la información del usuario en la base de datos.');
                }

                // 2b. Resetear contraseña si se proporcionó
                $newPassword = $_POST['new_password'] ?? '';
                if ($newPassword !== '') {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $this->userModel->updatePassword($id, $hashed);
                }

                // 3. Gestionar la asignación de roles
                // Primero, eliminar los roles actuales del usuario
                $this->roleModel->removeRolesFromUser($id);

                // Luego, asignar los nuevos roles seleccionados
                foreach ($roles as $roleId) {
                    $this->roleModel->assignRoleToUser($id, $roleId);
                }

                // 4. Lógica de Auditoría
                $newData = ['name' => $name, 'email' => $email, 'roles' => $roles, 'dedication' => $dedication, 'escuela' => $escuela];
                $oldData = ['name' => $oldUser['name'], 'email' => $oldUser['email'], 'roles' => $oldRoles, 'dedication' => $oldUser['dedicacion'] ?? ($oldUser['dedication'] ?? null), 'escuela' => $oldUser['escuela'] ?? null];
                $this->auditLogModel->logAction($_SESSION['user_id'], 'UPDATE', 'users', $id, $oldData, $newData);

                // 5. Registrar actividad en el log de actividades
                ActivityHelper::logUserUpdate($id, $name);

                // 6. Crear notificación
                NotificationHelper::notifyUserUpdate($id, $name);

                // Mensajes de éxito
                $_SESSION['flash_success'] = 'Usuario actualizado correctamente.';
                
                // Redirigir a la vista de gestión de usuarios
                header('Location: ' . BASE_PATH . '/users');
                exit();
            } catch (Exception $e) {
                error_log('UserController::update error: ' . $e->getMessage());
                error_log($e->getTraceAsString());
                $_SESSION['flash_error'] = 'Error al actualizar el usuario: ' . $e->getMessage();
                header('Location: ' . BASE_PATH . '/users/edit/' . $id);
                exit();
            }
        }
    }

    private function normalizeEscuela($escuela)
    {
        $escuela = strtoupper(trim((string) ($escuela ?? '')));

        if ($escuela === '') {
            return null;
        }

        return in_array($escuela, self::ESCUELA_OPTIONS, true) ? $escuela : null;
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $sessionRoles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('users', 'manage_all', $sessionRoles, '/users');

        try {
            // Obtener usuario a eliminar
            $user = $this->userModel->find($id);
            if (!$user) {
                $_SESSION['flash_error'] = 'Usuario no encontrado.';
                header('Location: ' . BASE_PATH . '/users');
                exit();
            }

            // Guardar datos antiguos para auditoría
            $oldData = ['name' => $user['name'], 'email' => $user['email']];

            // 1. Eliminar roles pivot
            $this->roleModel->removeRolesFromUser($id);

            // 2. Eliminar usuario
            if ($this->userModel->delete($id)) {
                // Auditoría
                $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'users', $id, $oldData, null);

                // Log de actividad y notificación
                ActivityHelper::logUserDelete($id, $user['name']);
                NotificationHelper::notifyUserDelete($id, $user['name']);
            }

            header('Location: ' . BASE_PATH . '/users');
            exit();
        } catch (Exception $e) {
            error_log('UserController::delete error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Ocurrió un error al eliminar el usuario. Detalle en logs.';
            header('Location: ' . BASE_PATH . '/users');
            exit();
        }
    }
}

