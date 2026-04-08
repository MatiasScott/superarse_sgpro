<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';

class AuthController
{
    private $userModel;
    private $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function showLogin()
    {
        $pageTitle = 'Login - SGPRO';
        // Cargar roles para el formulario de registro embebido en la pantalla de login
        $roles = $this->roleModel->getAll();
        // Por defecto no mostrar el panel de registro embebido
        $showRegisterPanel = false;
        $old = ['name' => '', 'email' => '', 'role' => ''];
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::validateRequest()) {
                http_response_code(419);
                $error = "Token CSRF inválido o expirado.";
                $pageTitle = 'Login - SGPRO';
                $roles = $this->roleModel->getAll();
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Usa el modelo para buscar el usuario por email
            $user = $this->userModel->findByEmail($email);
            $userrole = $user ? $this->roleModel->getRolesByUserId($user['id']) : [];
            $role = $userrole[0]['id'] ?? '';
            $roleNames = array_values(array_unique(array_column($userrole, 'role_name')));
            $roleIds = array_values(array_unique(array_column($userrole, 'id')));

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                // Autenticación exitosa
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $role;
                $_SESSION['role_names'] = $roleNames;
                $_SESSION['role_ids'] = $roleIds;
                
                // Redirige al dashboard
                header('Location: ' . BASE_PATH . '/dashboard');
                exit();
            } else {
                // Compatibilidad con usuarios creados antes del hashing:
                // si la contraseña almacenada coincide directamente con la proporcionada,
                // migramos el password a hash y autenticamos.
                if ($user && isset($user['password']) && $user['password'] === $password) {
                    // Re-hashear y actualizar la contraseña en la base de datos
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->userModel->updatePassword($user['id'], $newHash);

                    session_regenerate_id(true);
                    // Setear sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $role;
                    $_SESSION['role_names'] = $roleNames;
                    $_SESSION['role_ids'] = $roleIds;

                    header('Location: ' . BASE_PATH . '/dashboard');
                    exit();
                }
                // Autenticación fallida
                $error = "Correo o contraseña incorrectos.";
                $pageTitle = 'Login - SGPRO';
                // Cargar roles también para el panel de registro embebido
                $roles = $this->roleModel->getAll();
                require_once __DIR__ . '/../views/auth/login.php';
            }
        }
    }

    public function showRegister()
    {
        $pageTitle = 'Registro - SGPRO';
        // Usa el modelo para obtener todos los roles
        $roles = $this->roleModel->getAll();

        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::validateRequest()) {
                $error = "Token CSRF inválido o expirado.";
                $roles = $this->roleModel->getAll();
                $pageTitle = 'Registro - SGPRO';
                require_once __DIR__ . '/../views/auth/register.php';
                return;
            }

            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role_id = $_POST['role'] ?? null;
            $fromLogin = isset($_POST['from_login']) && $_POST['from_login'] == '1';

            if (empty($name) || empty($email) || empty($password) || empty($role_id)) {
                $error = "Todos los campos son obligatorios.";
                $roles = $this->roleModel->getAll();
                $pageTitle = 'Registro - SGPRO';
                if ($fromLogin) {
                    $showRegisterPanel = true;
                    $old = ['name' => $name, 'email' => $email, 'role' => $role_id];
                    require_once __DIR__ . '/../views/auth/login.php';
                } else {
                    require_once __DIR__ . '/../views/auth/register.php';
                }
                return;
            }

            // Encripta la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Usa el modelo de usuario para crear el nuevo registro
                $result = $this->userModel->create($name, $email, $hashedPassword);
                
                if (!$result) {
                    throw new Exception("No se pudo crear el usuario en la base de datos");
                }
                
                $user_id = $this->userModel->getLastInsertedId();
                
                if (!$user_id) {
                    throw new Exception("No se pudo obtener el ID del usuario creado");
                }

                // Usa el modelo de rol para asignar el rol
                $roleAssigned = $this->roleModel->assignRoleToUser($user_id, $role_id);
                
                if (!$roleAssigned) {
                    throw new Exception("No se pudo asignar el rol al usuario");
                }

                // Redirige al login después de un registro exitoso
                header('Location: ' . BASE_PATH . '/');
                exit();

            } catch (PDOException $e) {
                // Manejo de error si el email ya existe
                $error = "Error al registrar el usuario. El correo electrónico podría ya existir. Detalle: " . $e->getMessage();
                $roles = $this->roleModel->getAll();
                $pageTitle = 'Registro - SGPRO';
                if ($fromLogin) {
                    $showRegisterPanel = true;
                    $old = ['name' => $name, 'email' => $email, 'role' => $role_id];
                    require_once __DIR__ . '/../views/auth/login.php';
                } else {
                    require_once __DIR__ . '/../views/auth/register.php';
                }
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
                $roles = $this->roleModel->getAll();
                $pageTitle = 'Registro - SGPRO';
                if ($fromLogin) {
                    $showRegisterPanel = true;
                    $old = ['name' => $name, 'email' => $email, 'role' => $role_id];
                    require_once __DIR__ . '/../views/auth/login.php';
                } else {
                    require_once __DIR__ . '/../views/auth/register.php';
                }
            }
        }
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfHelper::validateRequest()) {
            http_response_code(419);
            require_once __DIR__ . '/../views/errors/http_error.php';
            exit();
        }

        unset($_SESSION['role_names'], $_SESSION['role_ids']);
        session_destroy();
        header('Location: ' . BASE_PATH . '/');
        exit();
    }
}