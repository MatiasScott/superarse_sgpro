<?php

// Ejecutar por escenario con PHP CLI. No usa una BD real ni guarda archivos.
require_once __DIR__ . '/../app/controllers/InvoiceController.php';

$scenario = $argv[1] ?? '';
$allowed = ['model', 'approved', 'unapproved', 'no-portfolio', 'wrong-pao', 'missing-unit',
    'invalid-unit', 'other-professor', 'database-error', 'admin',
    'director-create-for-professor',
    'update-approved', 'update-rejected', 'update-original', 'duplicate', 'duplicate-admin',
    'update-duplicate', 'update-duplicate-admin', 'invoice-check-error', 'duplicate-race'];
if (!in_array($scenario, $allowed, true)) {
    fwrite(STDERR, 'Escenario requerido: ' . implode(', ', $allowed) . PHP_EOL);
    exit(1);
}

if ($scenario === 'model') {
    $connection = new class extends PDO {
        public $query;
        public $params;
        public function __construct() {}
        public function prepare(string $query, array $options = []): PDOStatement|false
        {
            $this->query = $query;
            return new class($this) extends PDOStatement {
                private $connection;
                public function __construct($connection) { $this->connection = $connection; }
                public function execute(?array $params = null): bool
                {
                    $this->connection->params = $params;
                    return true;
                }
                public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
                {
                    return [['pao_id' => '10', 'unit_number' => '1'],
                        ['pao_id' => '10', 'unit_number' => '3'],
                        ['pao_id' => '12', 'unit_number' => '2']];
                }
            };
        }
    };
    $reflection = new ReflectionClass(PortfolioModel::class);
    $model = $reflection->newInstanceWithoutConstructor();
    $db = $reflection->getProperty('db');
    $db->setAccessible(true);
    $db->setValue($model, $connection);
    if ($model->getApprovedUnitsByProfessor(42) !== [10 => [1, 3], 12 => [2]]
        || $connection->params !== [42]
        || strpos($connection->query, 'unit_approved = 1') === false
        || strpos($connection->query, 'professor_id = ?') === false
        || strpos($connection->query, 'SELECT DISTINCT') === false) {
        throw new RuntimeException('Invalid approval query or result mapping');
    }
    echo "PASS: model\n";
    exit();
}

class InvoiceApprovalTestPortfolio extends PortfolioModel
{
    public $units = [10 => [1, 3]];
    public $fail = false;
    public $calls = [];

    public function __construct() {}

    public function getApprovedUnitsByProfessor($professorId)
    {
        $this->calls[] = $professorId;
        if ($this->fail) {
            throw new PDOException('Simulated portfolio query failure');
        }
        return $this->units;
    }
}

class InvoiceApprovalTestRoles extends RoleModel
{
    public $role;

    public function __construct($role) { $this->role = $role; }

    public function getRolesByUserId($id)
    {
        return [['role_name' => $this->role]];
    }
}

class InvoiceApprovalWriteReached extends Exception {}

class InvoiceApprovalTestInvoices extends InvoiceModel
{
    public $writes = 0;
    public $duplicate = false;
    public $failCheck = false;
    public $lateDuplicate = false;
    public $uniqueChecks = [];

    public function __construct() {}

    public function hasInvoiceForUnit($professorId, $paoId, $unitNumber, $excludeInvoiceId = null)
    {
        $this->uniqueChecks[] = [$professorId, $paoId, $unitNumber, $excludeInvoiceId];
        if ($this->failCheck) {
            throw new PDOException('Simulated invoice query failure');
        }
        return $this->duplicate;
    }
    public function find($id)
    {
        return ['id' => $id, 'professor_id' => 42, 'pao_id' => 10,
            'unit_number' => 2, 'period_month' => 'Enero', 'period_year' => 2026,
            'invoice_date' => '2026-10-13', 'amount' => '100', 'status' => 'Pendiente'];
    }

    public function create($professorId, $paoId, $unitNumber, $periodMonth, $periodYear, $invoiceDate, $amount, $status, $paymentProofPath = null, $comprobantePath = null, $observacion = null)
    {
        $this->writes++;
        if ($this->lateDuplicate) {
            throw new DuplicateInvoiceException('Ya existe una factura para este profesor, PAO y unidad.');
        }
        throw new InvoiceApprovalWriteReached();
    }

    public function update($id, $unitNumber, $periodMonth, $periodYear, $amount, $status, $paymentProofPath = null, $comprobantePath = null, $observacion = null)
    {
        $this->writes++;
        throw new InvoiceApprovalWriteReached();
    }

    public function updateProfessorAttachment($id, $professorId, $paymentProofPath, $observacion)
    {
        $this->writes++;
        throw new InvoiceApprovalWriteReached();
    }
}

// Aislar el test de la matriz de permisos que los administradores editan en la aplicacion.
$permissions = new ReflectionProperty(PermissionHelper::class, 'cachedPermissions');
$permissions->setAccessible(true);
$permissions->setValue(null, ['invoices' => [
    'create' => ['Profesor', 'Director de docencia'], 'edit' => ['Profesor'], 'manage_all' => ['Super Administrador']
]]);

define('BASE_PATH', '/sgpro');
session_start();
$_SESSION = ['user_id' => 42, 'csrf_token' => 'invoice-test-token'];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_FILES = [];
$_POST = ['_csrf' => 'invoice-test-token', 'professor_id' => '42',
    'pao_id' => '10', 'unit_number' => '3', 'amount' => '100'];

$portfolios = new InvoiceApprovalTestPortfolio();
$invoices = new InvoiceApprovalTestInvoices();
$isUpdate = strpos($scenario, 'update-') === 0;
$expectedWrite = in_array($scenario, ['approved', 'admin', 'director-create-for-professor', 'update-original'], true);

switch ($scenario) {
    case 'unapproved':
    case 'update-rejected':
        $_POST['unit_number'] = '4';
        break;
    case 'no-portfolio':
        $portfolios->units = [];
        break;
    case 'wrong-pao':
        $_POST['pao_id'] = '11';
        break;
    case 'missing-unit':
        unset($_POST['unit_number']);
        break;
    case 'invalid-unit':
        $_POST['unit_number'] = '1.5';
        break;
    case 'other-professor':
        $_POST['professor_id'] = '43';
        break;
    case 'database-error':
        $portfolios->fail = true;
        break;
    case 'admin':
        $portfolios->fail = true;
        $_POST['unit_number'] = '4';
        break;
    case 'director-create-for-professor':
        $_POST['professor_id'] = '43';
        break;
    case 'update-original':
        $_POST['unit_number'] = '2';
        break;
    case 'duplicate':
    case 'duplicate-admin':
    case 'update-duplicate':
    case 'update-duplicate-admin':
        $invoices->duplicate = true;
        break;
    case 'invoice-check-error':
        $invoices->failCheck = true;
        break;
    case 'duplicate-race':
        $invoices->lateDuplicate = true;
        break;
}

$reflection = new ReflectionClass(InvoiceController::class);
$controller = $reflection->newInstanceWithoutConstructor();
foreach (['portfolioModel' => $portfolios, 'invoiceModel' => $invoices,
    'roleModel' => new InvoiceApprovalTestRoles(in_array($scenario, ['admin', 'duplicate-admin', 'update-duplicate-admin'], true)
        ? 'Super Administrador'
        : ($scenario === 'director-create-for-professor' ? 'Director de docencia' : 'Profesor'))] as $name => $model) {
    $property = $reflection->getProperty($name);
    $property->setAccessible(true);
    $property->setValue($controller, $model);
}

$reachedWrite = false;
register_shutdown_function(function () use ($scenario, $expectedWrite, &$reachedWrite, $invoices, $portfolios) {
    $ok = $expectedWrite ? $reachedWrite && $invoices->writes === 1
        : $invoices->writes === ($scenario === 'duplicate-race' ? 1 : 0);
    if (!$expectedWrite && $scenario !== 'other-professor') {
        $ok = $ok && !empty($_SESSION['flash_error']);
    }
    if ($scenario === 'admin' || $scenario === 'director-create-for-professor' || $scenario === 'update-original') {
        $ok = $ok && $portfolios->calls === [];
    }
    if ($scenario === 'update-duplicate-admin') {
        $ok = $ok && $invoices->uniqueChecks[0][3] === 7;
    }
    if (!$ok) {
        fwrite(STDERR, "FAIL: $scenario\n");
        exit(1);
    }
    echo "PASS: $scenario\n";
});

try {
    if ($isUpdate) {
        $controller->update(7);
    } else {
        $controller->store();
    }
} catch (InvoiceApprovalWriteReached $e) {
    $reachedWrite = true;
}
