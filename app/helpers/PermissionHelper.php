<?php
// app/helpers/PermissionHelper.php

require_once __DIR__ . '/../models/RoleModel.php';

class PermissionHelper
{
    private const ROLE_SUPER_ADMIN = 'Super Administrador';
    private const ROLE_COORDINADOR = 'Coordinador académico';
    private const ROLE_DIRECTOR = 'Director de docencia';
    private const ROLE_TALENTO_HUMANO = 'Talento humano';
    private const ROLE_PROFESOR = 'Profesor';
    private static $cachedPermissions = null;

    private static function defaultPermissions()
    {
        return [
            'dashboard' => [
                'view' => self::allRoles(),
            ],
            'profile' => [
                'view' => self::allRoles(),
            ],
            'portfolios' => [
                'view' => self::allRoles(),
                'create' => [self::ROLE_PROFESOR],
                'edit' => [self::ROLE_PROFESOR],
                'delete' => [self::ROLE_PROFESOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR],
                'manage_own' => [self::ROLE_PROFESOR],
            ],
            'evaluations' => [
                'view' => self::allRoles(),
                'create' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR],
                'edit' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_PROFESOR],
                'delete' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_PROFESOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN],
                'manage_own' => [self::ROLE_PROFESOR],
            ],
            'continuity' => [
                'view' => self::allRoles(),
                'create' => [self::ROLE_PROFESOR],
                'edit' => [self::ROLE_PROFESOR],
                'delete' => [self::ROLE_PROFESOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO, self::ROLE_DIRECTOR, self::ROLE_COORDINADOR],
                'manage_own' => [self::ROLE_PROFESOR],
            ],
            'assignments' => [
                'view' => self::allRoles(),
                'create' => [self::ROLE_PROFESOR],
                'edit' => [self::ROLE_PROFESOR],
                'delete' => [self::ROLE_PROFESOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR],
                'manage_own' => [self::ROLE_PROFESOR],
            ],
            'contracts' => [
                'view' => self::allRoles(),
                'create' => [self::ROLE_PROFESOR],
                'edit' => [self::ROLE_PROFESOR],
                'delete' => [self::ROLE_PROFESOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
                'manage_own' => [self::ROLE_PROFESOR],
            ],
            'invoices' => [
                'view' => self::allRoles(),
                'create' => [self::ROLE_PROFESOR],
                'edit' => [self::ROLE_PROFESOR],
                'delete' => [self::ROLE_PROFESOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
                'manage_own' => [self::ROLE_PROFESOR],
            ],
            'reports' => [
                'view' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR, self::ROLE_TALENTO_HUMANO],
            ],
            'subjects' => [
                'view' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR, self::ROLE_TALENTO_HUMANO],
                'create' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR],
                'edit' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR],
                'delete' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR],
            ],
            'users' => [
                'view' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
                'create' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
                'edit' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
                'delete' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_TALENTO_HUMANO],
            ],
            'pao' => [
                'view' => [self::ROLE_SUPER_ADMIN, self::ROLE_DIRECTOR],
                'create' => [self::ROLE_SUPER_ADMIN, self::ROLE_DIRECTOR],
                'edit' => [self::ROLE_SUPER_ADMIN, self::ROLE_DIRECTOR],
                'delete' => [self::ROLE_SUPER_ADMIN, self::ROLE_DIRECTOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_DIRECTOR],
            ],
            'careers' => [
                'view' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR, self::ROLE_TALENTO_HUMANO],
                'create' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR],
                'edit' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR],
                'delete' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR],
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR],
            ],
            'notifications' => [
                'view' => self::allRoles(),
                'manage_all' => [self::ROLE_SUPER_ADMIN, self::ROLE_COORDINADOR, self::ROLE_DIRECTOR, self::ROLE_TALENTO_HUMANO],
            ],
            'permissions' => [
                'view' => [self::ROLE_SUPER_ADMIN],
                'manage_all' => [self::ROLE_SUPER_ADMIN],
            ],
        ];
    }

    private static function permissions()
    {
        if (self::$cachedPermissions !== null) {
            return self::$cachedPermissions;
        }

        $default = self::defaultPermissions();
        $configPath = self::getConfigPath();

        if (!file_exists($configPath)) {
            self::$cachedPermissions = $default;
            return self::$cachedPermissions;
        }

        $json = @file_get_contents($configPath);
        if ($json === false) {
            self::$cachedPermissions = $default;
            return self::$cachedPermissions;
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            self::$cachedPermissions = $default;
            return self::$cachedPermissions;
        }

        self::$cachedPermissions = self::normalizeMatrix($decoded, $default, true);
        return self::$cachedPermissions;
    }

    private static function getConfigPath()
    {
        return __DIR__ . '/../config/permissions.json';
    }

    private static function allRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_COORDINADOR,
            self::ROLE_DIRECTOR,
            self::ROLE_TALENTO_HUMANO,
            self::ROLE_PROFESOR,
        ];
    }

    private static function normalizeMatrix($input, $default, $useDefaultWhenMissing = true)
    {
        $allowedRoles = self::allRoles();
        $normalized = [];

        foreach ($default as $module => $actions) {
            $normalized[$module] = [];
            foreach ($actions as $action => $defaultRoles) {
                $moduleExists = array_key_exists($module, $input) && is_array($input[$module]);
                $actionExists = $moduleExists && array_key_exists($action, $input[$module]);

                if ($actionExists) {
                    $inputRoles = $input[$module][$action];
                } else {
                    $inputRoles = $useDefaultWhenMissing ? $defaultRoles : [];
                }

                if (!is_array($inputRoles)) {
                    $inputRoles = [];
                }

                $inputRoles = array_values(array_unique(array_filter($inputRoles, function ($role) use ($allowedRoles) {
                    return in_array($role, $allowedRoles, true);
                })));

                $normalized[$module][$action] = $inputRoles;
            }
        }

        return $normalized;
    }

    public static function getPermissionMatrix()
    {
        return self::permissions();
    }

    public static function getRolesCatalog()
    {
        return self::allRoles();
    }

    public static function savePermissionMatrix($matrix)
    {
        if (!is_array($matrix)) {
            return false;
        }

        $default = self::defaultPermissions();
        $normalized = self::normalizeMatrix($matrix, $default, false);
        $json = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            return false;
        }

        $path = self::getConfigPath();
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return false;
        }

        $written = @file_put_contents($path, $json);
        if ($written === false) {
            return false;
        }

        self::$cachedPermissions = $normalized;
        return true;
    }

    public static function getRoleNames($roles = null)
    {
        if (is_array($roles) && !empty($roles)) {
            return array_values(array_unique(array_column($roles, 'role_name')));
        }

        if (isset($_SESSION['role_names']) && is_array($_SESSION['role_names'])) {
            return $_SESSION['role_names'];
        }

        if (!isset($_SESSION['user_id'])) {
            return [];
        }

        $roleModel = new RoleModel();
        $currentRoles = $roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_values(array_unique(array_column($currentRoles, 'role_name')));
        $_SESSION['role_names'] = $roleNames;

        return $roleNames;
    }

    public static function hasAnyRole($allowedRoles, $roles = null)
    {
        if (!is_array($allowedRoles) || empty($allowedRoles)) {
            return false;
        }

        $roleNames = self::getRoleNames($roles);
        return count(array_intersect($roleNames, $allowedRoles)) > 0;
    }

    public static function can($module, $action = 'view', $roles = null)
    {
        $matrix = self::permissions();
        if (!isset($matrix[$module]) || !isset($matrix[$module][$action])) {
            return false;
        }

        return self::hasAnyRole($matrix[$module][$action], $roles);
    }

    public static function canAny($module, $actions = ['view'], $roles = null)
    {
        if (!is_array($actions) || empty($actions)) {
            return false;
        }

        foreach ($actions as $action) {
            if (self::can($module, $action, $roles)) {
                return true;
            }
        }

        return false;
    }

    public static function enforce($module, $action = 'view', $roles = null, $fallbackPath = '/dashboard')
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        if (!self::can($module, $action, $roles)) {
            header('Location: ' . BASE_PATH . $fallbackPath);
            exit();
        }
    }

    public static function enforceAny($module, $actions = ['view'], $roles = null, $fallbackPath = '/dashboard')
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        if (!self::canAny($module, $actions, $roles)) {
            header('Location: ' . BASE_PATH . $fallbackPath);
            exit();
        }
    }
}
