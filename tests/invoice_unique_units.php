<?php

// Ejecutar con PHP CLI. Verifica consultas y transacciones sin una BD real.
require_once __DIR__ . '/../app/models/InvoiceModel.php';

function checkUniqueInvoice($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

class UniqueInvoiceConnection extends PDO
{
    public $queries = [];
    public $events = [];
    public $duplicate = false;
    public $transaction = false;
    public $writes = 0;

    public function __construct() {}
    public function beginTransaction(): bool
    {
        $this->transaction = true;
        $this->events[] = 'begin';
        return true;
    }
    public function inTransaction(): bool { return $this->transaction; }
    public function commit(): bool
    {
        $this->transaction = false;
        $this->events[] = 'commit';
        return true;
    }
    public function rollBack(): bool
    {
        $this->transaction = false;
        $this->events[] = 'rollback';
        return true;
    }
    public function lastInsertId(?string $name = null): string|false
    {
        return $this->transaction ? '91' : '0';
    }
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        return new UniqueInvoiceStatement($this, $query);
    }
}

class UniqueInvoiceStatement extends PDOStatement
{
    private $connection;
    private $query;

    public function __construct($connection, $query)
    {
        $this->connection = $connection;
        $this->query = $query;
    }
    public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        return true;
    }
    public function execute(?array $params = null): bool
    {
        $this->connection->queries[] = [$this->query, $params];
        if (strpos($this->query, 'FROM users') !== false) {
            $this->connection->events[] = 'lock';
        } elseif (strpos($this->query, 'SELECT id FROM invoices') !== false) {
            $this->connection->events[] = 'check';
        } elseif (strpos($this->query, 'INSERT') === 0 || strpos($this->query, 'UPDATE') === 0) {
            $this->connection->events[] = 'write';
            $this->connection->writes++;
        }
        return true;
    }
    public function fetchColumn(int $column = 0): mixed
    {
        return strpos($this->query, 'FROM users') !== false ? 42 : ($this->connection->duplicate ? 8 : false);
    }
    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        return ['id' => 7, 'professor_id' => 42, 'pao_id' => 10, 'unit_number' => 2];
    }
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return [['professor_id' => '42', 'pao_id' => '10', 'unit_number' => '1'],
            ['professor_id' => '42', 'pao_id' => '10', 'unit_number' => '3'],
            ['professor_id' => '43', 'pao_id' => '11', 'unit_number' => '2']];
    }
}

foreach (['create', 'duplicate-create', 'update', 'duplicate-update', 'map'] as $scenario) {
    $connection = new UniqueInvoiceConnection();
    $connection->duplicate = strpos($scenario, 'duplicate-') === 0;
    $reflection = new ReflectionClass(InvoiceModel::class);
    $model = $reflection->newInstanceWithoutConstructor();
    $db = $reflection->getProperty('db');
    $db->setAccessible(true);
    $db->setValue($model, $connection);

    if ($scenario === 'map') {
        checkUniqueInvoice($model->getInvoicedUnitsByProfessor(42, 7) === [42 => [10 => [1, 3]], 43 => [11 => [2]]], 'Wrong map shape');
        [$query, $params] = $connection->queries[0];
        checkUniqueInvoice($params === [42, 7] && strpos($query, 'id <> ?') !== false, 'Missing professor filter or current invoice exclusion');
        checkUniqueInvoice(strpos($query, 'status') === false && strpos($query, 'period_month') === false, 'Unexpected month/status filter');
    } else {
        $isUpdate = strpos($scenario, 'update') !== false;
        $duplicateCaught = false;
        try {
            if ($isUpdate) {
                $model->update(7, 3, 'Febrero', 2026, 100, 'Pendiente');
            } else {
                $model->create(42, 10, 3, 'Febrero', 2026, '2026-10-13', 100, 'Pendiente');
            }
        } catch (DuplicateInvoiceException $e) {
            $duplicateCaught = true;
        }
        checkUniqueInvoice($duplicateCaught === $connection->duplicate, 'Duplicate not rejected correctly');
        $expectedEvents = $connection->duplicate ? ['begin', 'lock', 'check', 'rollback'] : ['begin', 'lock', 'check', 'write', 'commit'];
        checkUniqueInvoice($connection->events === $expectedEvents, 'Wrong locking or transaction order');
        checkUniqueInvoice($connection->writes === ($connection->duplicate ? 0 : 1), 'Unexpected database write');
        checkUniqueInvoice(!$connection->transaction, 'Open transaction');
        foreach ($connection->queries as [$query, $params]) {
            if (strpos($query, 'INSERT INTO invoices') === 0) {
                checkUniqueInvoice(strpos($query, 'invoice_upload_enabled) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)') !== false, 'New invoices must start disabled');
            }
            if (strpos($query, 'SELECT id FROM invoices') === 0) {
                checkUniqueInvoice(strpos($query, 'FOR UPDATE') !== false, 'Duplicate query must use current locking read');
                checkUniqueInvoice($params === ($isUpdate ? [42, 10, 3, 7] : [42, 10, 3]), 'Incorrect uniqueness parameters');
            }
        }
        if (!$isUpdate && !$connection->duplicate) {
            checkUniqueInvoice($model->getLastInsertedId() === '91', 'Insert ID lost after commit');
        }
    }
    echo "PASS: $scenario\n";
}
