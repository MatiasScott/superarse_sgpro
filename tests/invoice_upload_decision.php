<?php
require_once __DIR__ . '/../app/controllers/InvoiceController.php';

$scenario = $argv[1] ?? '';
$scenarios = ['matrix', 'admin', 'director', 'professor', 'coordinator', 'hr', 'revoked',
    'invalid-value', 'missing-invoice', 'invalid-csrf', 'get', 'database-error', 'enable'];
if (!in_array($scenario, $scenarios, true)) {
    fwrite(STDERR, implode(', ', $scenarios) . PHP_EOL);
    exit(1);
}
if ($scenario === 'matrix') {
    $matrix = PermissionHelper::getPermissionMatrix();
    if (($matrix['invoices']['manage_upload_permission'] ?? null) !== ['Super Administrador', 'Director de docencia']) {
        throw new RuntimeException('Default decision roles are incorrect');
    }
    $reflection = new ReflectionClass(PermissionHelper::class);
    $method = $reflection->getMethod('normalizeMatrix');
    $method->setAccessible(true);
    $matrix['invoices']['manage_upload_permission'] = PermissionHelper::getRolesCatalog();
    $normalized = $method->invoke(null, $matrix, PermissionHelper::getPermissionMatrix());
    if ($normalized['invoices']['manage_upload_permission'] !== ['Super Administrador', 'Director de docencia']) {
        throw new RuntimeException('Unauthorized roles accepted');
    }
    $matrix['invoices']['manage_upload_permission'] = [];
    $normalized = $method->invoke(null, $matrix, PermissionHelper::getPermissionMatrix());
    if ($normalized['invoices']['manage_upload_permission'] !== []) {
        throw new RuntimeException('Explicit revocation ignored');
    }
    echo "PASS: matrix\n";
    exit();
}

class DecisionRoles extends RoleModel
{
    public $actor;
    public $target = 'Profesor';
    public function __construct($actor) { $this->actor = $actor; }
    public function getRolesByUserId($id)
    {
        return [['role_name' => (int) $id === 42 ? $this->target : $this->actor]];
    }
}
class DecisionInvoices extends InvoiceModel
{
    public $saved = [];
    public $fail = false;
    public function __construct() {}
    public $missing = false;
    public function find($id)
    {
        if ($this->fail) { throw new PDOException('Simulated flag query failure'); }
        return $this->missing ? false : ['id' => $id, 'professor_id' => 50, 'invoice_upload_enabled' => 1];
    }
    public function setInvoiceUploadPermission($id, $enabled)
    {
        $this->saved[] = [$id, $enabled];
        return true;
    }
}
class DecisionAudit extends AuditLogModel
{
    public $logs = [];
    public function __construct() {}
    public function logAction($userId, $action, $tableName, $recordId, $oldData = null, $newData = null)
    {
        $this->logs[] = [$oldData, $newData];
        return true;
    }
}
define('BASE_PATH', '/sgpro');
session_start();
$_SESSION = ['user_id' => 1, 'csrf_token' => 'decision'];
$_POST = ['_csrf' => 'decision', 'invoice_upload_enabled' => '0'];
$_SERVER['REQUEST_METHOD'] = 'POST';
$role = ['director' => 'Director de docencia', 'professor' => 'Profesor',
    'coordinator' => 'Coordinador académico', 'hr' => 'Talento humano'][$scenario] ?? 'Super Administrador';
$roles = new DecisionRoles($role);
$users = new DecisionInvoices();
$audit = new DecisionAudit();
if ($scenario === 'invalid-value') { $_POST['invoice_upload_enabled'] = '2'; }
if ($scenario === 'missing-invoice') { $users->missing = true; }
if ($scenario === 'enable') { $_POST['invoice_upload_enabled'] = '1'; }
if ($scenario === 'invalid-csrf') { $_POST['_csrf'] = 'wrong'; }
if ($scenario === 'get') { $_SERVER['REQUEST_METHOD'] = 'GET'; }
if ($scenario === 'database-error') { $users->fail = true; }
if ($scenario === 'revoked') {
    $matrix = PermissionHelper::getPermissionMatrix();
    $matrix['invoices']['manage_upload_permission'] = [];
    $property = new ReflectionProperty(PermissionHelper::class, 'cachedPermissions');
    $property->setAccessible(true);
    $property->setValue(null, $matrix);
}
$reflection = new ReflectionClass(InvoiceController::class);
$controller = $reflection->newInstanceWithoutConstructor();
foreach (['roleModel' => $roles, 'invoiceModel' => $users, 'auditLogModel' => $audit] as $name => $model) {
    $property = $reflection->getProperty($name);
    $property->setAccessible(true);
    $property->setValue($controller, $model);
}
register_shutdown_function(function () use ($scenario, $users, $audit) {
    $save = in_array($scenario, ['admin', 'director', 'enable'], true);
    $flag = $scenario === 'enable' ? 1 : 0;
    $ok = $save ? $users->saved === [[42, $flag]]
        && $audit->logs === [[['invoice_upload_enabled' => 1], ['invoice_upload_enabled' => $flag]]]
        && !empty($_SESSION['flash_success']) : $users->saved === [] && $audit->logs === [];
    if (in_array($scenario, ['invalid-value', 'missing-invoice', 'database-error'], true)) {
        $ok = $ok && !empty($_SESSION['flash_error']);
    }
    echo ($ok ? 'PASS: ' : 'FAIL: ') . $scenario . PHP_EOL;
    if (!$ok) { exit(1); }
});
$controller->updateUploadPermission(42);
