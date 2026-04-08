# Modulo de permisos RBAC (SGPRO)

## Objetivo

Centralizar permisos por rol y por vista para evitar inconsistencias entre:

- menu/sidebar,
- botones y acciones en vistas,
- y controladores.

## Archivo central

La matriz de permisos se define en:

- `app/helpers/PermissionHelper.php`

## Estructura

Cada modulo tiene acciones, por ejemplo:

- `view`
- `create`
- `manage_all`
- `manage_own`

Ejemplo de uso:

```php
require_once __DIR__ . '/../../helpers/PermissionHelper.php';

$canManageUsers = PermissionHelper::can('users', 'manage_all', $roles ?? null);

if ($canManageUsers) {
    // mostrar botón o ejecutar acción
}
```

Para forzar permiso en controlador:

```php
PermissionHelper::enforce('users', 'manage_all', $roles, '/users');
```

## Sesion de roles

Al iniciar sesion se guardan:

- `$_SESSION['role_names']`
- `$_SESSION['role_ids']`

Esto permite evaluar permisos de forma consistente.

## Modulos migrados a RBAC

- Sidebar: `app/views/partials/sidebar.php`
- Usuarios: `app/controllers/UserController.php`, `app/views/users/index.php`
- Evaluaciones: `app/controllers/EvaluationController.php`, `app/views/evaluations/index.php`
- Asignaciones: `app/views/academic/assignments.php`
- Materias: `app/views/academic/subjects.php`
- Contratos: `app/views/contracts/index.php`
- Facturas: `app/views/invoices/index.php`
- Portafolios: `app/views/portfolios/index.php`
- PAO: `app/views/pao/index.php`, `app/views/pao/edit.php`
- Carreras: `app/views/academic/careers.php`
- Continuidad: `app/views/continuity/index.php`

## Recomendacion de mantenimiento

Cuando se cree una nueva vista/modulo:

1. Agregar su matriz en `PermissionHelper::permissions()`.
2. Usar `PermissionHelper::can()` en la vista.
3. Usar `PermissionHelper::enforce()` en el controlador.
