<?php
// app/controllers/InvoiceController.php

require_once __DIR__ . '/../models/InvoiceModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';

class InvoiceController
{
    private $invoiceModel;
    private $userModel;
    private $paoModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->userModel = new UserModel();
        $this->paoModel = new PaoModel();
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
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
        
        // Talento Humano, Super Administrador y Profesores pueden crear facturas
        $roleNames = array_column($roles, 'role_name');
        if (!in_array('Talento humano', $roleNames) && !in_array('Super Administrador', $roleNames) && !in_array('Profesor', $roleNames)) {
            header('Location: ' . BASE_PATH . '/invoices');
            exit();
        }
        
        // Obtener profesores para el formulario
        $professors = $this->userModel->getUsersByRole('Profesor');
        
        // Obtener PAOs para el formulario
        $paos = $this->paoModel->getAll();
        
        $pageTitle = 'Crear Nueva Factura';
        require_once __DIR__ . '/../views/invoices/create-invoice.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener datos directamente del formulario
            $professorId = $_POST['professor_id'] ?? null;
            $paoId = $_POST['pao_id'] ?? null;
            $unitNumber = $_POST['unit_number'] ?? 1;
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
                        $newFileName = md5(time() . $fileName) . '.pdf';
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
                        $newFileName = md5(time() . $fileName) . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $comprobantePath = '/uploads/comprobantes/' . $newFileName;
                        }
                    }
                }
            }

            if ($this->invoiceModel->create($professorId, $paoId, $unitNumber, $periodMonth, $periodYear, $invoiceDate, $amount, $status, $paymentProofPath, $comprobantePath, $observacion)) {
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
        $roleNames = array_column($roles, 'role_name');
        
        // Talento Humano, Super Administrador y Profesores pueden editar facturas
        if (!in_array('Talento humano', $roleNames) && !in_array('Super Administrador', $roleNames) && !in_array('Profesor', $roleNames)) {
            header('Location: ' . BASE_PATH . '/invoices');
            exit();
        }
        
        $invoice = $this->invoiceModel->find($id);

        if (!$invoice) {
            header('Location: ' . BASE_PATH . '/invoices');
            exit();
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
        require_once __DIR__ . '/../views/invoices/edit-invoice.php';
    }

    // Método para procesar la actualización de la factura
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldInvoice = $this->invoiceModel->find($id);
            if (!$oldInvoice) {
                echo "Factura no encontrada.";
                exit();
            }

            $unitNumber = $_POST['unit_number'] ?? $oldInvoice['unit_number'];
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
                        $newFileName = md5(time() . $fileName) . '.pdf';
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
                        $newFileName = md5(time() . $fileName) . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $comprobantePath = '/uploads/comprobantes/' . $newFileName;
                        }
                    }
                }
            }

            if ($this->invoiceModel->update($id, $unitNumber, $periodMonth, $periodYear, $amount, $status, $paymentProofPath, $comprobantePath, $observacion)) {
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

        $invoice = $this->invoiceModel->find((int)$id);
        if ($invoice) {
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
