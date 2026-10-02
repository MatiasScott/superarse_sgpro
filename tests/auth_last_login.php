<?php

// Ejecutar con PHP CLI, una vez por escenario indicado abajo. No usa una BD real.
require_once __DIR__ . '/../app/controllers/AuthController.php';

$scenario = $argv[1] ?? '';
$scenarios = ['hashed', 'legacy', 'invalid-password', 'unknown-user', 'invalid-csrf', 'database-error'];
if (!in_array($scenario, $scenarios, true)) {
    fwrite(STDERR, 'Escenario requerido: ' . implode(', ', $scenarios) . PHP_EOL);
    exit(1);
}

function checkLastLogin($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

class LastLoginTestStatement extends PDOStatement
{
    public $calls = [];
    public $closed = false;
    public $fail = false;

    public function __construct()
    {
    }

    public function execute(?array $params = null): bool
    {
        $this->calls[] = $params;
        if ($this->fail) {
            throw new PDOException('Simulated last-login database failure');
        }
        return true;
    }

    public function closeCursor(): bool
    {
        $this->closed = true;
        return true;
    }
}

class LastLoginTestConnection extends PDO
{
    public $statement;
    public $queries = [];

    public function __construct()
    {
        $this->statement = new LastLoginTestStatement();
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->queries[] = $query;
        return $this->statement;
    }
}

class LastLoginTestUserModel extends UserModel
{
    public $user;
    public $passwordUpdates = [];

    public function __construct($connection, $user)
    {
        $this->db = $connection;
        $this->user = $user;
    }

    public function findByEmail($email)
    {
        return $this->user;
    }

    public function updatePassword($id, $hashedPassword)
    {
        $this->passwordUpdates[] = [$id, $hashedPassword];
        return true;
    }
}

class LastLoginTestRoleModel extends RoleModel
{
    public function __construct()
    {
    }

    public function getRolesByUserId($userId)
    {
        return [['id' => 5, 'role_name' => 'Profesor']];
    }

    public function getAll()
    {
        return $this->getRolesByUserId(42);
    }
}

define('BASE_PATH', '/sgpro');
session_start();
$_SESSION = ['csrf_token' => 'test-token'];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'email' => 'test@example.invalid',
    'password' => $scenario === 'invalid-password' ? 'wrong-password' : 'test-password',
    '_csrf' => $scenario === 'invalid-csrf' ? 'wrong-token' : 'test-token',
];

$connection = new LastLoginTestConnection();
$connection->statement->fail = $scenario === 'database-error';
$user = $scenario === 'unknown-user' ? null : [
    'id' => 42,
    'name' => 'Test User',
    'password' => $scenario === 'legacy' ? 'test-password' : password_hash('test-password', PASSWORD_DEFAULT),
];
$userModel = new LastLoginTestUserModel($connection, $user);
$reflection = new ReflectionClass(AuthController::class);
$controller = $reflection->newInstanceWithoutConstructor();
foreach (['userModel' => $userModel, 'roleModel' => new LastLoginTestRoleModel()] as $name => $value) {
    $property = $reflection->getProperty($name);
    $property->setValue($controller, $value);
}

ob_start();
// login() termina con exit en los accesos correctos; validar tambien desde shutdown.
register_shutdown_function(function () use ($scenario, $connection, $userModel) {
    ob_end_clean();
    try {
        $success = in_array($scenario, ['hashed', 'legacy', 'database-error'], true);
        checkLastLogin($connection->queries === ($success ? ['CALL sp_registrar_ultimo_acceso(?)'] : []), 'Procedimiento incorrecto o llamado en un login fallido');
        checkLastLogin($connection->statement->calls === ($success ? [[42]] : []), 'ID incorrecto o registro duplicado');
        checkLastLogin($connection->statement->closed === $success, 'El cursor del procedimiento debe cerrarse incluso si falla');
        checkLastLogin(isset($_SESSION['user_id']) === $success, 'Cambio inesperado en la autenticacion');
        if ($success) {
            checkLastLogin($_SESSION['user_id'] === 42 && $_SESSION['role_ids'] === [5], 'Sesion o roles incorrectos');
        }
        checkLastLogin(count($userModel->passwordUpdates) === ($scenario === 'legacy' ? 1 : 0), 'Cambio inesperado en la migracion de contrasenas');
        if ($scenario === 'legacy') {
            checkLastLogin(password_verify('test-password', $userModel->passwordUpdates[0][1]), 'La contrasena antigua no se migro a hash');
        }
        echo 'PASS: ' . $scenario . PHP_EOL;
    } catch (Throwable $e) {
        fwrite(STDERR, 'FAIL: ' . $scenario . ': ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
});

$controller->login();
