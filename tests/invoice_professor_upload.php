<?php

// PHP CLI por escenario. No modifica archivos ni la BD; usa la matriz real de permisos.
require_once __DIR__ . '/../app/controllers/InvoiceController.php';

$scenario = $argv[1] ?? '';
$allowed = ['model', 'observation', 'disabled-observation', 'disabled-pdf', 'permission-error',
    'tamper-unit', 'tamper-month', 'tamper-year', 'tamper-amount', 'tamper-status',
    'tamper-date', 'tamper-professor', 'tamper-pao', 'tamper-comprobante', 'tamper-flag',
    'invalid-observation', 'invalid-pdf', 'upload-error', 'other-owner', 'write-error'];
if (!in_array($scenario, $allowed, true)) {
    fwrite(STDERR, 'Escenario requerido: ' . implode(', ', $allowed) . PHP_EOL);
    exit(1);
}

if ($scenario === 'model') {
    $connection = new class extends PDO {
        public $queries = [];
        public $flag = '1';
        public function __construct() {}
        public function prepare(string $query, array $options = []): PDOStatement|false
        {
            return new class($this, $query) extends PDOStatement {
                private $connection;
                private $query;
                public function __construct($connection, $query) { $this->connection = $connection; $this->query = $query; }
                public function execute(?array $params = null): bool
                {
                    $this->connection->queries[] = [$this->query, $params];
                    return true;
                }
                public function fetchColumn(int $column = 0): mixed { return $this->connection->flag; }
            };
        }
    };
    $models = [];
    foreach ([UserModel::class, InvoiceModel::class] as $class) {
        $reflection = new ReflectionClass($class);
        $models[$class] = $reflection->newInstanceWithoutConstructor();
        $db = $reflection->getProperty('db');
        $db->setAccessible(true);
        $db->setValue($models[$class], $connection);
    }
    $model = $models[InvoiceModel::class];
    if (!$model->canUploadInvoice(7)) { throw new RuntimeException('Flag 1 must enable uploads'); }
    $connection->flag = '0';
    if ($model->canUploadInvoice(7)) { throw new RuntimeException('Flag 0 must block uploads'); }
    $model->setInvoiceUploadPermission(7, 0);
    [$query, $params] = end($connection->queries);
    if ($query !== 'UPDATE invoices SET invoice_upload_enabled = ? WHERE id = ?' || $params !== [0, 7]) {
        throw new RuntimeException('Upload flag must be persisted per invoice');
    }
    $invalidRejected = false;
    try { $model->setInvoiceUploadPermission(7, 2); }
    catch (InvalidArgumentException $e) { $invalidRejected = true; }
    if (!$invalidRejected) { throw new RuntimeException('Invalid flag accepted'); }
    $models[InvoiceModel::class]->updateProfessorAttachment(7, 42, '/uploads/proofs/test.pdf', 'Nota');
    [$query, $params] = end($connection->queries);
    if ($query !== 'UPDATE invoices SET payment_proof_path = ?, observacion = ? WHERE id = ? AND professor_id = ?'
        || $params !== ['/uploads/proofs/test.pdf', 'Nota', 7, 42]) {
        throw new RuntimeException('Professor update must only write PDF/observation and check ownership');
    }
    echo "PASS: model\n";
    exit();
}

class ProfessorUploadRoles extends RoleModel
{
    public function __construct() {}
    public function getRolesByUserId($id) { return [['role_name' => 'Profesor']]; }
}

class ProfessorUploadInvoices extends InvoiceModel
{
    public $enabled = true;
    public $permissionFail = false;
    public $checks = [];
    public $saved = [];
    public $fail = false;
    public $owner = 42;
    public function __construct() {}
    public function canUploadInvoice($id)
    {
        $this->checks[] = $id;
        if ($this->permissionFail) {
            throw new PDOException('Simulated permission query failure');
        }
        return $this->enabled;
    }
    public function find($id)
    {
        return ['id' => $id, 'professor_id' => $this->owner, 'pao_id' => 10, 'unit_number' => 2,
            'period_month' => 'Enero', 'period_year' => 2026, 'invoice_date' => '2026-10-13',
            'amount' => '100.00', 'status' => 'Pagada', 'payment_proof_path' => '/uploads/proofs/original.pdf',
            'comprobante_path' => '/uploads/comprobantes/original.pdf', 'observacion' => 'Original',
            'invoice_upload_enabled' => (int) $this->enabled];
    }
    public function updateProfessorAttachment($id, $professorId, $paymentProofPath, $observacion)
    {
        if ($this->fail) {
            throw new PDOException('Simulated attachment save failure');
        }
        $this->saved[] = [$id, $professorId, $paymentProofPath, $observacion];
        return true;
    }
    public function update($id, $unitNumber, $periodMonth, $periodYear, $amount, $status, $paymentProofPath = null, $comprobantePath = null, $observacion = null)
    {
        throw new LogicException('Docentes no deben invocar la actualizacion de datos financieros.');
    }
}

class ProfessorUploadAudit extends AuditLogModel
{
    public $logs = [];
    public function __construct() {}
    public function logAction($userId, $action, $tableName, $recordId, $oldData = null, $newData = null)
    {
        $this->logs[] = $newData;
        return true;
    }
}

define('BASE_PATH', '/sgpro');
session_start();
$_SESSION = ['user_id' => 42, 'csrf_token' => 'upload-test'];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['_csrf' => 'upload-test', 'observacion' => 'Nota del docente'];
$_FILES = [];
$invoices = new ProfessorUploadInvoices();
$audit = new ProfessorUploadAudit();

switch ($scenario) {
    case 'disabled-observation': $invoices->enabled = false; break;
    case 'disabled-pdf':
    case 'permission-error':
    case 'upload-error':
    case 'invalid-pdf':
        $invoices->enabled = $scenario !== 'disabled-pdf';
        $invoices->permissionFail = $scenario === 'permission-error';
        $_FILES['payment_proof'] = ['name' => 'factura.txt', 'tmp_name' => '', 'size' => 10,
            'error' => $scenario === 'upload-error' ? UPLOAD_ERR_INI_SIZE : UPLOAD_ERR_OK];
        break;
    case 'tamper-unit': $_POST['unit_number'] = '3'; break;
    case 'tamper-month': $_POST['period_month'] = 'Febrero'; break;
    case 'tamper-year': $_POST['period_year'] = '2025'; break;
    case 'tamper-amount': $_POST['amount'] = '999'; break;
    case 'tamper-status': $_POST['status'] = 'Rechazada'; break;
    case 'tamper-date': $_POST['invoice_date'] = '2025-01-01'; break;
    case 'tamper-professor': $_POST['professor_id'] = '43'; break;
    case 'tamper-pao': $_POST['pao_id'] = '11'; break;
    case 'tamper-comprobante': $_FILES['comprobante'] = ['error' => UPLOAD_ERR_OK]; break;
    case 'tamper-flag': $_POST['invoice_upload_enabled'] = '0'; break;
    case 'invalid-observation': $_POST['observacion'] = ['No es texto']; break;
    case 'other-owner': $invoices->owner = 43; break;
    case 'write-error': $invoices->fail = true; break;
}

$reflection = new ReflectionClass(InvoiceController::class);
$controller = $reflection->newInstanceWithoutConstructor();
foreach (['invoiceModel' => $invoices,
    'roleModel' => new ProfessorUploadRoles(), 'auditLogModel' => $audit] as $name => $model) {
    $property = $reflection->getProperty($name);
    $property->setAccessible(true);
    $property->setValue($controller, $model);
}

register_shutdown_function(function () use ($scenario, $invoices, $audit) {
    $saves = in_array($scenario, ['observation', 'disabled-observation'], true);
    $ok = $saves
        ? $invoices->saved === [[7, 42, '/uploads/proofs/original.pdf', 'Nota del docente']]
            && $audit->logs === [['payment_proof_path' => '/uploads/proofs/original.pdf', 'observacion' => 'Nota del docente']]
        : $invoices->saved === [] && ($scenario === 'other-owner' || !empty($_SESSION['flash_error']));
    if (in_array($scenario, ['disabled-pdf', 'permission-error'], true)) {
        $ok = $ok && !empty($_SESSION['invoice_upload_blocked']) && $invoices->checks === [7];
    }
    echo ($ok ? 'PASS: ' : 'FAIL: ') . $scenario . PHP_EOL;
    if (!$ok) { exit(1); }
});
$controller->update(7);
