<?php
// app/controllers/InvoiceController.php

require_once __DIR__ . '/../models/InvoiceModel.php';
require_once __DIR__ . '/../models/PortfolioModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class InvoiceController
{
    private $invoiceModel;
    private $portfolioModel;
    private $userModel;
    private $paoModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->portfolioModel = new PortfolioModel();
        $this->userModel = new UserModel();
        $this->paoModel = new PaoModel();
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    private function canManageAllInvoices($roles)
    {
        return PermissionHelper::can('invoices', 'manage_all', $roles);
    }

    private function denyAccessToInvoices()
    {
        header('Location: ' . BASE_PATH . '/invoices');
        exit();
    }

    public function updateUploadPermission($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('invoices', 'manage_upload_permission', $roles, '/invoices');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfHelper::validateRequest()) {
            http_response_code($_SERVER['REQUEST_METHOD'] !== 'POST' ? 405 : 419);
            echo 'Solicitud inválida. Se requiere POST y un token CSRF válido.';
            exit();
        }
        try {
            $value = $_POST['invoice_upload_enabled'] ?? null;
            if (!in_array($value, ['0', '1'], true)) {
                throw new InvalidArgumentException('Seleccione 1 - Habilitado o 0 - Deshabilitado.');
            }
            $invoice = $this->invoiceModel->find($id);
            if (!$invoice) {
                throw new InvalidArgumentException('Factura no encontrada.');
            }
            if (!array_key_exists('invoice_upload_enabled', $invoice)) {
                throw new RuntimeException('Debe aplicar la migración de habilitación por factura.');
            }
            $oldEnabled = (int) $invoice['invoice_upload_enabled'];
            if (!$this->invoiceModel->setInvoiceUploadPermission($id, (int) $value)) {
                throw new RuntimeException('No se pudo guardar la habilitación de la factura.');
            }
            $this->auditLogModel->logAction(
                $_SESSION['user_id'], 'UPDATE', 'invoices', $id,
                ['invoice_upload_enabled' => $oldEnabled], ['invoice_upload_enabled' => (int) $value]
            );
            $_SESSION['flash_success'] = 'Carga del PDF ' . ($value === '1' ? 'habilitada' : 'deshabilitada') . ' para esta factura.';
        } catch (PDOException | RuntimeException | InvalidArgumentException $e) {
            error_log('InvoiceController::updateUploadPermission error: ' . $e->getMessage());
            $_SESSION['flash_error'] = $e instanceof PDOException
                ? 'No se pudo guardar la habilitación. Detalle en logs.' : $e->getMessage();
        }
        header('Location: ' . BASE_PATH . '/invoices/edit/' . $id);
        exit();
    }

    private function loadInvoiceUploadPermission($invoiceId)
    {
        try {
            return $this->invoiceModel->canUploadInvoice($invoiceId);
        } catch (PDOException | RuntimeException $e) {
            error_log('InvoiceController::loadInvoiceUploadPermission error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'No se pudo verificar el permiso de carga de facturas. Contacte al administrador.';
            return false;
        }
    }

    private function rejectProfessorInvoiceUpdate($message, $id)
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_PATH . '/invoices/edit/' . $id);
        exit();
    }

    private function storeInvoicePdf($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo recibir el PDF de la factura. Revise el tamaño y vuelva a intentar.');
        }
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf'
            || (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) !== 'application/pdf') {
            throw new RuntimeException('La factura debe ser un archivo PDF válido.');
        }
        if ($file['size'] > 50 * 1024 * 1024) {
            throw new RuntimeException('La factura PDF no debe superar los 50 MB.');
        }
        $directory = __DIR__ . '/../../public/uploads/proofs/';
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException('No se pudo preparar el directorio de facturas.');
        }
        $name = bin2hex(random_bytes(16)) . '.pdf';
        if (!move_uploaded_file($file['tmp_name'], $directory . $name)) {
            throw new RuntimeException('No se pudo guardar el PDF de la factura.');
        }
        return '/uploads/proofs/' . $name;
    }

    private function updateProfessorInvoice($id, array $oldInvoice)
    {
        foreach (['professor_id', 'pao_id', 'unit_number', 'period_month', 'period_year',
            'invoice_date', 'amount', 'status', 'invoice_upload_enabled'] as $field) {
            if (array_key_exists($field, $_POST)
                && (!is_scalar($_POST[$field]) || (string) $_POST[$field] !== (string) $oldInvoice[$field])) {
                $this->rejectProfessorInvoiceUpdate('Como docente solo puede modificar el PDF de la factura y la observación.', $id);
            }
        }
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] !== UPLOAD_ERR_NO_FILE) {
            $this->rejectProfessorInvoiceUpdate('El comprobante solo puede ser modificado por un administrador.', $id);
        }
        $path = $oldInvoice['payment_proof_path'] ?? null;
        $newPath = null;
        $observation = $_POST['observacion'] ?? ($oldInvoice['observacion'] ?? null);
        if ($observation !== null && !is_string($observation)) {
            $this->rejectProfessorInvoiceUpdate('La observación debe ser un texto válido.', $id);
        }
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
            if (!$this->loadInvoiceUploadPermission($id)) {
                $_SESSION['invoice_upload_blocked'] = true;
                $this->rejectProfessorInvoiceUpdate(
                    $_SESSION['flash_error'] ?? 'No se encuentra habilitada la opción de subir factura. Contacte al administrador.', $id
                );
            }
        }
        try {
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
                $newPath = $this->storeInvoicePdf($_FILES['payment_proof']);
                $path = $newPath;
            }
            if (!$this->invoiceModel->updateProfessorAttachment($id, $_SESSION['user_id'], $path, $observation)) {
                throw new RuntimeException('No se pudo actualizar el PDF o la observación de la factura.');
            }
        } catch (PDOException | RuntimeException $e) {
            error_log('InvoiceController::updateProfessorInvoice error: ' . $e->getMessage());
            if ($newPath !== null && !unlink(__DIR__ . '/../../public' . $newPath)) {
                error_log('InvoiceController::updateProfessorInvoice: no se pudo limpiar ' . $newPath);
            }
            $this->rejectProfessorInvoiceUpdate('No se pudo actualizar la factura. ' . ($e instanceof PDOException ? 'Detalle en logs.' : $e->getMessage()), $id);
        }
        $this->auditLogModel->logAction(
            $_SESSION['user_id'], 'UPDATE', 'invoices', $id,
            ['payment_proof_path' => $oldInvoice['payment_proof_path'] ?? null, 'observacion' => $oldInvoice['observacion'] ?? null],
            ['payment_proof_path' => $path, 'observacion' => $observation]
        );
        $_SESSION['flash_success'] = 'PDF y observación de la factura actualizados correctamente.';
        header('Location: ' . BASE_PATH . '/invoices');
        exit();
    }

    private function loadApprovedUnits($professorId)
    {
        try {
            return $this->portfolioModel->getApprovedUnitsByProfessor($professorId);
        } catch (PDOException $e) {
            error_log('InvoiceController::loadApprovedUnits error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'No se pudo verificar la aprobación de los portafolios. Intente nuevamente.';
            return null;
        }
    }

    private function enforceApprovedUnit($professorId, $paoId, $unitNumber, $redirect)
    {
        $approvedUnits = $this->loadApprovedUnits($professorId);
        $unit = filter_var($unitNumber, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 4]
        ]);
        if ($approvedUnits !== null && $unit !== false
            && in_array($unit, $approvedUnits[(int) $paoId] ?? [], true)) {
            return;
        }

        if ($approvedUnits !== null) {
            $_SESSION['flash_error'] = 'Solo puede facturar unidades aprobadas de su portafolio en el PAO seleccionado.';
        }
        header('Location: ' . BASE_PATH . $redirect);
        exit();
    }

    private function loadInvoicedUnits($professorId = null, $excludeInvoiceId = null)
    {
        try {
            return $this->invoiceModel->getInvoicedUnitsByProfessor($professorId, $excludeInvoiceId);
        } catch (PDOException $e) {
            error_log('InvoiceController::loadInvoicedUnits error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'No se pudo verificar las unidades ya facturadas. Intente nuevamente.';
            return null;
        }
    }

    private function enforceUniqueUnit($professorId, $paoId, $unitNumber, $redirect, $excludeInvoiceId = null)
    {
        try {
            if (!$this->invoiceModel->hasInvoiceForUnit($professorId, $paoId, $unitNumber, $excludeInvoiceId)) {
                return;
            }
            $_SESSION['flash_error'] = 'Ya existe una factura para este profesor, PAO y unidad. No puede facturar la misma unidad nuevamente.';
        } catch (PDOException $e) {
            error_log('InvoiceController::enforceUniqueUnit error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'No se pudo verificar las unidades ya facturadas. Intente nuevamente.';
        }
        header('Location: ' . BASE_PATH . $redirect);
        exit();
    }

    private function rejectDuplicateInvoice(DuplicateInvoiceException $e, array $uploadedPaths, $redirect)
    {
        foreach ($uploadedPaths as $path) {
            if ($path !== null && !unlink(__DIR__ . '/../../public' . $path)) {
                error_log('InvoiceController::rejectDuplicateInvoice: no se pudo limpiar ' . $path);
            }
        }
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . BASE_PATH . $redirect);
        exit();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $roles = $this->roleModel->getRolesByUserId($userId);

        // Obtener facturas filtradas según el rol del usuario
        $invoices = $this->invoiceModel->getInvoicesWithDetails($userId, $roles);

        $pageTitle = 'Gestión de Facturas';

        require_once __DIR__ . '/../views/invoices/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        if (!PermissionHelper::canAny('invoices', ['create', 'manage_all'], $roles)) {
            header('Location: ' . BASE_PATH . '/invoices');
            exit();
        }
        
        // Obtener profesores para el formulario
        $professors = $this->userModel->getUsersByRole('Profesor');
        
        // Obtener PAOs para el formulario
        $paos = $this->paoModel->getAll();
        $restrictInvoiceUnits = !$this->canManageAllInvoices($roles);
        $approvedUnitsByPao = $restrictInvoiceUnits
            ? $this->loadApprovedUnits($_SESSION['user_id']) : [];
        $invoicedUnitsByProfessor = $this->loadInvoicedUnits($restrictInvoiceUnits ? $_SESSION['user_id'] : null);
        $invoiceUploadEnabled = !$restrictInvoiceUnits;
        
        $pageTitle = 'Crear Nueva Factura';
        require_once __DIR__ . '/../views/invoices/create-invoice.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::validateRequest()) {
                http_response_code(419);
                echo 'Token CSRF inválido o expirado.';
                exit();
            }

            // Obtener datos directamente del formulario
            $professorId = $_POST['professor_id'] ?? null;
            $paoId = $_POST['pao_id'] ?? null;
            $unitNumber = $_POST['unit_number'] ?? null;
            $periodMonth = $_POST['period_month'] ?? null;
            $periodYear = $_POST['period_year'] ?? date('Y');
            $invoiceDate = $_POST['invoice_date'] ?? date('Y-m-d');
            $amount = $_POST['amount'] ?? null;
            $status = $_POST['status'] ?? 'Pendiente';
            $observacion = $_POST['observacion'] ?? null;
            $paymentProofPath = null;
            $comprobantePath = null;
            
            // Validar datos requeridos
            if (!$professorId || !$paoId) {
                echo "Error: Debe seleccionar un profesor y un PAO.";
                return;
            }

            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_PATH . '/');
                exit();
            }

            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
            $canManageAll = $this->canManageAllInvoices($roles);
            if ($canManageAll && $unitNumber === null) {
                $unitNumber = 1;
            }
            if (!PermissionHelper::canAny('invoices', ['create', 'manage_all'], $roles)) {
                $this->denyAccessToInvoices();
            }
            if (!$canManageAll && (int)$professorId !== (int)$_SESSION['user_id']) {
                $this->denyAccessToInvoices();
            }
            if (!$canManageAll) {
                $this->enforceApprovedUnit($professorId, $paoId, $unitNumber, '/invoices/create');
            }
            $this->enforceUniqueUnit($professorId, $paoId, $unitNumber, '/invoices/create');
            if (!$canManageAll && isset($_FILES['payment_proof'])
                && $_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
                $_SESSION['flash_error'] = $_SESSION['flash_error'] ?? 'No se encuentra habilitada la opción de subir factura. Contacte al administrador.';
                $_SESSION['invoice_upload_blocked'] = true;
                header('Location: ' . BASE_PATH . '/invoices/create');
                exit();
            }

            // Manejar archivo de factura PDF
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/proofs/';
                $fileTmpPath = $_FILES['payment_proof']['tmp_name'];
                $fileName = $_FILES['payment_proof']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                if ($fileExtension === 'pdf') {
                    $maxFileSize = 50 * 1024 * 1024; // 50MB
                    if ($_FILES['payment_proof']['size'] <= $maxFileSize) {
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $newFileName = bin2hex(random_bytes(16)) . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $paymentProofPath = '/uploads/proofs/' . $newFileName;
                        }
                    }
                }
            }

            // Manejar archivo de comprobante PDF
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/comprobantes/';
                $fileTmpPath = $_FILES['comprobante']['tmp_name'];
                $fileName = $_FILES['comprobante']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                if ($fileExtension === 'pdf') {
                    $maxFileSize = 50 * 1024 * 1024; // 50MB
                    if ($_FILES['comprobante']['size'] <= $maxFileSize) {
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $newFileName = bin2hex(random_bytes(16)) . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $comprobantePath = '/uploads/comprobantes/' . $newFileName;
                        }
                    }
                }
            }

            try {
                $created = $this->invoiceModel->create($professorId, $paoId, $unitNumber, $periodMonth, $periodYear, $invoiceDate, $amount, $status, $paymentProofPath, $comprobantePath, $observacion);
            } catch (DuplicateInvoiceException $e) {
                $this->rejectDuplicateInvoice($e, [$paymentProofPath, $comprobantePath], '/invoices/create');
            }
            if ($created) {
                $lastInvoiceId = $this->invoiceModel->getLastInsertedId();
                $userId = $_SESSION['user_id'] ?? null;
                $newData = [
                    'professor_id' => $professorId, 
                    'pao_id' => $paoId,
                    'unit_number' => $unitNumber,
                    'period_month' => $periodMonth,
                    'period_year' => $periodYear,
                    'amount' => $amount, 
                    'status' => $status,
                    'comprobante_path' => $comprobantePath,
                    'observacion' => $observacion
                ];
                $this->auditLogModel->logAction($userId, 'CREATE', 'invoices', $lastInvoiceId, null, $newData);

                // Registrar actividad en el log de actividades
                $professorName = '';
                $professor = $this->userModel->find($professorId);
                if ($professor) $professorName = $professor['name'];
                ActivityHelper::logInvoiceCreate($lastInvoiceId, $professorName, $amount);

                // Crear notificación para administradores y para el profesor
                NotificationHelper::notifyInvoiceCreate($lastInvoiceId, $professorName, $amount, $professorId);

                header('Location: ' . BASE_PATH . '/invoices');
                exit();
            } else {
                echo "Error al guardar la factura.";
            }
        }
    }

    // Método para mostrar el formulario de edición
    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        if (!PermissionHelper::canAny('invoices', ['edit', 'manage_all', 'manage_upload_permission'], $roles)) {
            header('Location: ' . BASE_PATH . '/invoices');
            exit();
        }
        
        $invoice = $this->invoiceModel->find($id);

        if (!$invoice) {
            header('Location: ' . BASE_PATH . '/invoices');
            exit();
        }
        $canDecideInvoiceUpload = PermissionHelper::can('invoices', 'manage_upload_permission', $roles);
        if (!$this->canManageAllInvoices($roles) && !$canDecideInvoiceUpload
            && (int) $invoice['professor_id'] !== (int) $_SESSION['user_id']) {
            $this->denyAccessToInvoices();
        }

        // Obtener el nombre del profesor usando el ID de la factura
        $professor = $this->userModel->find($invoice['professor_id']);

        // Si el profesor existe, pasar su nombre a la vista
        if ($professor) {
            $invoice['professor_name'] = $professor['name'];
        } else {
            $invoice['professor_name'] = 'Desconocido';
        }

        // Obtener también el nombre del PAO
        $pao = $this->paoModel->find($invoice['pao_id']);
        if ($pao) {
            $invoice['pao_name'] = $pao['title'];
        } else {
            $invoice['pao_name'] = 'Desconocido';
        }

        $pageTitle = 'Editar Factura: ' . htmlspecialchars($invoice['id']);
        $restrictInvoiceUnits = !$this->canManageAllInvoices($roles);
        $approvedUnitsByPao = [];
        $invoicedUnitsByProfessor = $restrictInvoiceUnits ? [] : $this->loadInvoicedUnits($invoice['professor_id'], $id);
        $invoiceUploadEnabled = !$restrictInvoiceUnits || (
            (int) $invoice['professor_id'] === (int) $_SESSION['user_id'] && $this->loadInvoiceUploadPermission($id)
        );
        require_once __DIR__ . '/../views/invoices/edit-invoice.php';
    }

    // Método para procesar la actualización de la factura
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::validateRequest()) {
                http_response_code(419);
                echo 'Token CSRF inválido o expirado.';
                exit();
            }

            $oldInvoice = $this->invoiceModel->find($id);
            if (!$oldInvoice) {
                echo "Factura no encontrada.";
                exit();
            }

            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_PATH . '/');
                exit();
            }

            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
            $canManageAll = $this->canManageAllInvoices($roles);
            if (!PermissionHelper::canAny('invoices', ['edit', 'manage_all'], $roles)) {
                $this->denyAccessToInvoices();
            }
            if (!$canManageAll && (int)$oldInvoice['professor_id'] !== (int)$_SESSION['user_id']) {
                $this->denyAccessToInvoices();
            }
            if (!$canManageAll) {
                $this->updateProfessorInvoice($id, $oldInvoice);
                return;
            }

            $unitNumber = $_POST['unit_number'] ?? $oldInvoice['unit_number'];
            $this->enforceUniqueUnit($oldInvoice['professor_id'], $oldInvoice['pao_id'], $unitNumber, '/invoices/edit/' . $id, $id);
            $periodMonth = $_POST['period_month'] ?? $oldInvoice['period_month'];
            $periodYear = $_POST['period_year'] ?? $oldInvoice['period_year'];
            $amount = $_POST['amount'] ?? null;
            $status = $_POST['status'] ?? 'Pendiente';
            $observacion = $_POST['observacion'] ?? null;
            $paymentProofPath = $oldInvoice['payment_proof_path'] ?? null;
            $comprobantePath = $oldInvoice['comprobante_path'] ?? null;

            // Manejar archivo de factura PDF
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/proofs/';
                $fileTmpPath = $_FILES['payment_proof']['tmp_name'];
                $fileName = $_FILES['payment_proof']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                if ($fileExtension === 'pdf') {
                    $maxFileSize = 50 * 1024 * 1024; // 50MB
                    if ($_FILES['payment_proof']['size'] <= $maxFileSize) {
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $newFileName = bin2hex(random_bytes(16)) . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $paymentProofPath = '/uploads/proofs/' . $newFileName;
                        }
                    }
                }
            }

            // Manejar archivo de comprobante PDF
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/comprobantes/';
                $fileTmpPath = $_FILES['comprobante']['tmp_name'];
                $fileName = $_FILES['comprobante']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                if ($fileExtension === 'pdf') {
                    $maxFileSize = 50 * 1024 * 1024; // 50MB
                    if ($_FILES['comprobante']['size'] <= $maxFileSize) {
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $newFileName = bin2hex(random_bytes(16)) . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $comprobantePath = '/uploads/comprobantes/' . $newFileName;
                        }
                    }
                }
            }

            try {
                $updated = $this->invoiceModel->update($id, $unitNumber, $periodMonth, $periodYear, $amount, $status, $paymentProofPath, $comprobantePath, $observacion);
            } catch (DuplicateInvoiceException $e) {
                $this->rejectDuplicateInvoice($e, [
                    $paymentProofPath !== ($oldInvoice['payment_proof_path'] ?? null) ? $paymentProofPath : null,
                    $comprobantePath !== ($oldInvoice['comprobante_path'] ?? null) ? $comprobantePath : null
                ], '/invoices/edit/' . $id);
            }
            if ($updated) {
                $userId = $_SESSION['user_id'] ?? null;
                $newData = [
                    'unit_number' => $unitNumber,
                    'period_month' => $periodMonth,
                    'period_year' => $periodYear,
                    'amount' => $amount, 
                    'status' => $status, 
                    'payment_proof_path' => $paymentProofPath,
                    'comprobante_path' => $comprobantePath,
                    'observacion' => $observacion
                ];
                $oldData = [
                    'unit_number' => $oldInvoice['unit_number'],
                    'period_month' => $oldInvoice['period_month'],
                    'period_year' => $oldInvoice['period_year'],
                    'amount' => $oldInvoice['amount'], 
                    'status' => $oldInvoice['status'], 
                    'payment_proof_path' => $oldInvoice['payment_proof_path'],
                    'comprobante_path' => $oldInvoice['comprobante_path'] ?? null,
                    'observacion' => $oldInvoice['observacion'] ?? null
                ];
                $this->auditLogModel->logAction($userId, 'UPDATE', 'invoices', $id, $oldData, $newData);

                header('Location: ' . BASE_PATH . '/invoices');
                exit();
            } else {
                echo "Error al actualizar la factura.";
            }
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        if (!CsrfHelper::validateRequest()) {
            http_response_code(419);
            echo 'Token CSRF inválido o expirado.';
            exit();
        }

        $invoice = $this->invoiceModel->find((int)$id);
        if ($invoice) {
            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
            $canManageAll = $this->canManageAllInvoices($roles);
            if (!PermissionHelper::canAny('invoices', ['delete', 'manage_all'], $roles)) {
                $this->denyAccessToInvoices();
            }
            if (!$canManageAll && (int)$invoice['professor_id'] !== (int)$_SESSION['user_id']) {
                $this->denyAccessToInvoices();
            }

            $files = ['invoice_file_path', 'receipt_file_path'];
            foreach ($files as $field) {
                if (!empty($invoice[$field]) && file_exists(__DIR__ . '/../../public' . $invoice[$field])) {
                    unlink(__DIR__ . '/../../public' . $invoice[$field]);
                }
            }
            
            $query = "DELETE FROM invoices WHERE id = ?";
            $stmt = $this->invoiceModel->getConnection()->prepare($query);
            $stmt->execute([(int)$id]);
            $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'invoices', (int)$id, $invoice, null);
        }

        header('Location: ' . BASE_PATH . '/invoices');
        exit();
    }
}
