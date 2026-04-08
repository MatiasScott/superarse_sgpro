<?php
// app/controllers/ContractController.php

require_once __DIR__ . '/../models/ContractModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class ContractController
{
    private $contractModel;
    private $userModel;
    private $roleModel;
    private $paoModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->contractModel = new ContractModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->paoModel = new PaoModel();
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
        $contracts = $this->contractModel->getContractsWithDetails($userId, $roles);
        $pageTitle = 'Gestión de Contratos';
        require_once __DIR__ . '/../views/contracts/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforceAny('contracts', ['create', 'manage_all'], $roles, '/contracts');
        $professors = $this->userModel->getUsersByRole('Profesor');
        $paos = $this->paoModel->getAll();
        $pageTitle = 'Crear Nuevo Contrato';
        require_once __DIR__ . '/../views/contracts/create-contract.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id'] ?? 0);
            PermissionHelper::enforceAny('contracts', ['create', 'manage_all'], $roles, '/contracts');

            $professorId = $_POST['professor_id'] ?? null;
            $paoId = $_POST['pao_id'] ?? null;
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            $status = $_POST['status'] ?? 'Activo';
            $documentPath = null;

            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/contracts/';
                $fileName = uniqid() . '_' . basename($_FILES['document']['name']);
                $documentPath = $uploadDir . $fileName;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                move_uploaded_file($_FILES['document']['tmp_name'], $documentPath);
            }

            if ($this->contractModel->create($professorId, $paoId, $startDate, $endDate, $status, $documentPath)) {
                $lastContractId = $this->contractModel->getLastInsertedId();
                $userIdLog = $_SESSION['user_id'] ?? null;
                $newData = ['professor_id' => $professorId, 'pao_id' => $paoId, 'start_date' => $startDate, 'end_date' => $endDate, 'status' => $status, 'document_path' => $documentPath];
                $this->auditLogModel->logAction($userIdLog, 'CREATE', 'contracts', $lastContractId, null, $newData);

                // Registrar actividad en el log de actividades
                $professorName = '';
                $professor = $this->userModel->find($professorId);
                if ($professor) $professorName = $professor['name'];
                ActivityHelper::logContractCreate($lastContractId, $professorName);

                // Crear notificación
                NotificationHelper::notifyContractCreate($lastContractId, $professorName);

                header('Location: ' . BASE_PATH . '/contracts');
                exit();
            } else {
                echo "Error al guardar el contrato.";
            }
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $contract = $this->contractModel->find($id);

        if (!$contract) {
            header('Location: ' . BASE_PATH . '/contracts');
            exit();
        }

        $canManageAll = PermissionHelper::can('contracts', 'manage_all', $roles);
        $canEdit = PermissionHelper::can('contracts', 'edit', $roles);
        $isOwner = (int)($_SESSION['user_id'] ?? 0) === (int)($contract['professor_id'] ?? 0);
        if (!$canManageAll && !($canEdit && $isOwner)) {
            header('Location: ' . BASE_PATH . '/contracts');
            exit();
        }

        $professors = $this->userModel->getUsersByRole('Profesor');
        $paos = $this->paoModel->getAll();
        $pageTitle = 'Editar Contrato: ' . htmlspecialchars($contract['id']);

        $professor = $this->userModel->find($contract['professor_id']);
        if ($professor) {
            $contract['professor_name'] = $professor['name'];
        } else {
            $contract['professor_name'] = 'Desconocido';
        }

        $pao = $this->paoModel->find($contract['pao_id']);
        if ($pao) {
            $contract['pao_name'] = $pao['title'];
        } else {
            $contract['pao_name'] = 'Desconocido';
        }

        require_once __DIR__ . '/../views/contracts/edit-contract.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contract = $this->contractModel->find((int)$id);
            if (!$contract) {
                header('Location: ' . BASE_PATH . '/contracts');
                exit();
            }

            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id'] ?? 0);
            $canManageAll = PermissionHelper::can('contracts', 'manage_all', $roles);
            $canEdit = PermissionHelper::can('contracts', 'edit', $roles);
            $isOwner = (int)($_SESSION['user_id'] ?? 0) === (int)($contract['professor_id'] ?? 0);
            if (!$canManageAll && !($canEdit && $isOwner)) {
                header('Location: ' . BASE_PATH . '/contracts');
                exit();
            }

            // 1. Obtener los datos del formulario
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            $status = $_POST['status'];

            // 2. Lógica para manejar la subida del archivo
            $newDocumentPath = null;
            if (isset($_FILES['new_contract_file']) && $_FILES['new_contract_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['new_contract_file']['tmp_name'];
                $fileName = $_FILES['new_contract_file']['name'];
                $fileSize = $_FILES['new_contract_file']['size'];
                $fileType = $_FILES['new_contract_file']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = ['pdf', 'doc', 'docx'];
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadDir = __DIR__ . '/../../public/uploads/contracts/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // La ruta que se guarda en la DB NO debe incluir la carpeta `public`.
                        // Se agrega el '/' inicial para que sea una ruta absoluta desde la carpeta `public`.
                        $newDocumentPath = '/uploads/contracts/' . $newFileName;
                    }
                }
            }

            // 3. Llamar al modelo para actualizar el contrato
            $success = $this->contractModel->updateContract($id, $startDate, $endDate, $status, $newDocumentPath);

            if ($success) {
                // Redirigir a la vista de contratos con un mensaje de éxito
                header('Location: ' . BASE_PATH . '/contracts');
                exit;
            } else {
                // Manejar el error de actualización
                // ...
            }
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $contract = $this->contractModel->find((int)$id);
        if (!$contract) {
            header('Location: ' . BASE_PATH . '/contracts');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canManageAll = PermissionHelper::can('contracts', 'manage_all', $roles);
        $canDelete = PermissionHelper::can('contracts', 'delete', $roles);
        $isOwner = (int)($_SESSION['user_id'] ?? 0) === (int)($contract['professor_id'] ?? 0);
        if (!$canManageAll && !($canDelete && $isOwner)) {
            header('Location: ' . BASE_PATH . '/contracts');
            exit();
        }

        if ($contract) {
            if (!empty($contract['file_path']) && file_exists(__DIR__ . '/../../public' . $contract['file_path'])) {
                unlink(__DIR__ . '/../../public' . $contract['file_path']);
            }
            
            $query = "DELETE FROM contracts WHERE id = ?";
            $stmt = $this->contractModel->getConnection()->prepare($query);
            $stmt->execute([(int)$id]);
            $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'contracts', (int)$id, $contract, null);
        }

        header('Location: ' . BASE_PATH . '/contracts');
        exit();
    }
}
