<?php
// app/controllers/PermissionController.php

require_once __DIR__ . '/../helpers/PermissionHelper.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ExcelExportHelper.php';

class PermissionController
{
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    private function getHistoryPath()
    {
        return __DIR__ . '/../config/permissions_history.jsonl';
    }

    private function getHistoryFiltersFromRequest()
    {
        $dateFrom = trim((string)($_GET['date_from'] ?? ''));
        $dateTo = trim((string)($_GET['date_to'] ?? ''));
        $user = trim((string)($_GET['user'] ?? ''));
        $module = trim((string)($_GET['module'] ?? ''));

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'user' => $user,
            'module' => $module,
        ];
    }

    private function normalizeDateTimeBoundary($date, $isEnd)
    {
        if ($date === '') {
            return null;
        }

        $suffix = $isEnd ? ' 23:59:59' : ' 00:00:00';
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $date . $suffix);

        return $dt ?: null;
    }

    private function historyEntryMatchesFilters($entry, $filters)
    {
        $entryTimestamp = trim((string)($entry['timestamp'] ?? ''));
        $entryDate = DateTime::createFromFormat('Y-m-d H:i:s', $entryTimestamp);

        $fromDate = $this->normalizeDateTimeBoundary($filters['date_from'] ?? '', false);
        if ($fromDate && (!$entryDate || $entryDate < $fromDate)) {
            return false;
        }

        $toDate = $this->normalizeDateTimeBoundary($filters['date_to'] ?? '', true);
        if ($toDate && (!$entryDate || $entryDate > $toDate)) {
            return false;
        }

        $toLower = function ($value) {
            $value = (string)$value;
            return function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
        };

        $userFilter = $toLower(trim((string)($filters['user'] ?? '')));
        if ($userFilter !== '') {
            $userName = $toLower((string)($entry['user_name'] ?? ''));
            $userId = $toLower((string)($entry['user_id'] ?? ''));
            if (strpos($userName, $userFilter) === false && strpos($userId, $userFilter) === false) {
                return false;
            }
        }

        $moduleFilter = trim((string)($filters['module'] ?? ''));
        if ($moduleFilter !== '') {
            $hasModule = false;
            $entryChanges = is_array($entry['changes'] ?? null) ? $entry['changes'] : [];
            foreach ($entryChanges as $change) {
                if (($change['module'] ?? '') === $moduleFilter) {
                    $hasModule = true;
                    break;
                }
            }

            if (!$hasModule) {
                return false;
            }
        }

        return true;
    }

    private function buildChanges($oldMatrix, $newMatrix)
    {
        $changes = [];

        foreach ($oldMatrix as $module => $actions) {
            foreach ($actions as $action => $oldRoles) {
                $newRoles = $newMatrix[$module][$action] ?? [];

                $added = array_values(array_diff($newRoles, $oldRoles));
                $removed = array_values(array_diff($oldRoles, $newRoles));

                if (!empty($added) || !empty($removed)) {
                    $changes[] = [
                        'module' => $module,
                        'action' => $action,
                        'added' => $added,
                        'removed' => $removed,
                    ];
                }
            }
        }

        return $changes;
    }

    private function appendHistory($entry)
    {
        $path = $this->getHistoryPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }

    private function readHistory($limit = 30, $filters = [])
    {
        $path = $this->getHistoryPath();
        if (!file_exists($path)) {
            return [];
        }

        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) {
            return [];
        }

        $lines = array_slice($lines, -$limit);
        $entries = [];
        foreach (array_reverse($lines) as $line) {
            $decoded = json_decode($line, true);
            if (is_array($decoded) && $this->historyEntryMatchesFilters($decoded, $filters)) {
                $entries[] = $decoded;
                if (count($entries) >= $limit) {
                    break;
                }
            }
        }

        return $entries;
    }

    private function getHistoryRowsForExport($entries, $moduleLabels, $actionLabels)
    {
        $rows = [];
        $rows[] = ['HISTORIAL DE CAMBIOS DE PERMISOS'];
        $rows[] = ['Generado: ' . date('Y-m-d H:i:s')];
        $rows[] = [];
        $rows[] = ['Fecha', 'Usuario', 'ID Usuario', 'Modulo', 'Accion', 'Agregados', 'Removidos'];

        foreach ($entries as $entry) {
            $timestamp = $entry['timestamp'] ?? '';
            $userName = $entry['user_name'] ?? 'Desconocido';
            $userId = (string)($entry['user_id'] ?? 'N/A');
            $changes = is_array($entry['changes'] ?? null) ? $entry['changes'] : [];

            if (empty($changes)) {
                $rows[] = [$timestamp, $userName, $userId, 'N/A', 'N/A', 'Ninguno', 'Ninguno'];
                continue;
            }

            foreach ($changes as $change) {
                $moduleKey = (string)($change['module'] ?? '');
                $actionKey = (string)($change['action'] ?? '');
                $added = !empty($change['added']) ? implode(', ', $change['added']) : 'Ninguno';
                $removed = !empty($change['removed']) ? implode(', ', $change['removed']) : 'Ninguno';

                $rows[] = [
                    $timestamp,
                    $userName,
                    $userId,
                    $moduleLabels[$moduleKey] ?? $moduleKey,
                    $actionLabels[$actionKey] ?? $actionKey,
                    $added,
                    $removed,
                ];
            }
        }

        return $rows;
    }

    private function outputHistoryPdf($entries, $moduleLabels, $actionLabels)
    {
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            http_response_code(500);
            echo 'No se encontró la librería de PDF. Ejecuta composer install.';
            exit;
        }

        require_once $autoloadPath;

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
            . 'body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937;}'
            . 'h1{font-size:16px;margin:0 0 8px 0;} p{margin:0 0 12px 0;}'
            . 'table{width:100%;border-collapse:collapse;} th,td{border:1px solid #d1d5db;padding:6px;vertical-align:top;}'
            . 'th{background:#f3f4f6;text-align:left;}'
            . '</style></head><body>';
        $html .= '<h1>Historial de cambios de permisos</h1>';
        $html .= '<p>Generado: ' . htmlspecialchars(date('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8') . '</p>';
        $html .= '<table><thead><tr>'
            . '<th>Fecha</th><th>Usuario</th><th>ID Usuario</th><th>Módulo</th><th>Acción</th><th>Agregados</th><th>Removidos</th>'
            . '</tr></thead><tbody>';

        if (empty($entries)) {
            $html .= '<tr><td colspan="7">No hay cambios para los filtros seleccionados.</td></tr>';
        } else {
            foreach ($entries as $entry) {
                $timestamp = (string)($entry['timestamp'] ?? '');
                $userName = (string)($entry['user_name'] ?? 'Desconocido');
                $userId = (string)($entry['user_id'] ?? 'N/A');
                $changes = is_array($entry['changes'] ?? null) ? $entry['changes'] : [];

                if (empty($changes)) {
                    $html .= '<tr><td>' . htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>N/A</td><td>N/A</td><td>Ninguno</td><td>Ninguno</td></tr>';
                    continue;
                }

                foreach ($changes as $change) {
                    $moduleKey = (string)($change['module'] ?? '');
                    $actionKey = (string)($change['action'] ?? '');
                    $added = !empty($change['added']) ? implode(', ', $change['added']) : 'Ninguno';
                    $removed = !empty($change['removed']) ? implode(', ', $change['removed']) : 'Ninguno';

                    $html .= '<tr>'
                        . '<td>' . htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($moduleLabels[$moduleKey] ?? $moduleKey, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($actionLabels[$actionKey] ?? $actionKey, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($added, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($removed, ENT_QUOTES, 'UTF-8') . '</td>'
                        . '</tr>';
                }
            }
        }

        $html .= '</tbody></table></body></html>';

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('historial_permisos_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    private function getModuleLabels()
    {
        return [
            'dashboard' => 'Dashboard',
            'profile' => 'Mi Perfil',
            'portfolios' => 'Portafolios',
            'evaluations' => 'Evaluaciones',
            'continuity' => 'Continuidad',
            'assignments' => 'Asignaciones',
            'contracts' => 'Contratos',
            'invoices' => 'Facturas',
            'reports' => 'Reportes',
            'subjects' => 'Materias',
            'users' => 'Gestión de Usuarios',
            'pao' => 'PAO',
            'careers' => 'Carreras',
            'notifications' => 'Notificaciones',
            'permissions' => 'Permisos',
        ];
    }

    private function getActionLabels()
    {
        return [
            'view' => 'Ver módulo',
            'create' => 'Crear',
            'edit' => 'Editar',
            'delete' => 'Eliminar',
            'manage_all' => 'Gestionar todo',
            'manage_own' => 'Gestionar propio (global)',
        ];
    }

    private function ensureAdminAccess()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        if (!PermissionHelper::can('permissions', 'manage_all', $roles)) {
            header('Location: ' . BASE_PATH . '/dashboard');
            exit();
        }
    }

    public function index()
    {
        $this->ensureAdminAccess();

        $permissionMatrix = PermissionHelper::getPermissionMatrix();
        $rolesCatalog = PermissionHelper::getRolesCatalog();
        $moduleLabels = $this->getModuleLabels();
        $actionLabels = $this->getActionLabels();
        $historyFilters = $this->getHistoryFiltersFromRequest();
        $historyEntries = $this->readHistory(200, $historyFilters);

        $pageTitle = 'Administración de Permisos';
        require_once __DIR__ . '/../views/permissions/index.php';
    }

    public function exportHistoryExcel()
    {
        $this->ensureAdminAccess();

        $moduleLabels = $this->getModuleLabels();
        $actionLabels = $this->getActionLabels();
        $historyFilters = $this->getHistoryFiltersFromRequest();
        $entries = $this->readHistory(5000, $historyFilters);
        $rows = $this->getHistoryRowsForExport($entries, $moduleLabels, $actionLabels);

        ExcelExportHelper::createPermissionHistoryReport($rows, 'historial_permisos_' . date('Ymd_His') . '.xlsx');
    }

    public function exportHistoryPdf()
    {
        $this->ensureAdminAccess();

        $moduleLabels = $this->getModuleLabels();
        $actionLabels = $this->getActionLabels();
        $historyFilters = $this->getHistoryFiltersFromRequest();
        $entries = $this->readHistory(5000, $historyFilters);

        $this->outputHistoryPdf($entries, $moduleLabels, $actionLabels);
    }

    public function update()
    {
        $this->ensureAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/permissions');
            exit();
        }

        $posted = $_POST['permissions'] ?? [];
        $oldMatrix = PermissionHelper::getPermissionMatrix();
        $saved = PermissionHelper::savePermissionMatrix($posted);

        if ($saved) {
            $newMatrix = PermissionHelper::getPermissionMatrix();
            $changes = $this->buildChanges($oldMatrix, $newMatrix);

            if (!empty($changes)) {
                $entry = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'user_name' => $_SESSION['user_name'] ?? 'Desconocido',
                    'changes' => $changes,
                ];

                $this->appendHistory($entry);

                // Auditoría en BD
                $this->auditLogModel->logAction(
                    $_SESSION['user_id'] ?? null,
                    'UPDATE',
                    'permissions',
                    1,
                    $oldMatrix,
                    $newMatrix
                );
            }

            $_SESSION['flash_success'] = 'Permisos actualizados correctamente.';
        } else {
            $_SESSION['flash_error'] = 'No se pudieron guardar los permisos.';
        }

        header('Location: ' . BASE_PATH . '/permissions');
        exit();
    }
}
