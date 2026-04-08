<?php
// app/controllers/ContinuityController.php

require_once __DIR__ . '/../models/ContinuityModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class ContinuityController
{
    private $continuityModel;
    private $userModel;
    private $paoModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->continuityModel = new ContinuityModel();
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
        $continuities = $this->continuityModel->getContinuitiesWithDetails($userId, $roles);
        $pageTitle = 'Gestión de Continuidad';
        require_once __DIR__ . '/../views/continuity/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $professors = $this->userModel->getUsersByRole('Profesor');
        $paos = $this->paoModel->getAll();
        $pageTitle = 'Registrar Decisión de Continuidad';
        require_once __DIR__ . '/../views/continuity/create-continuity.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_POST['professor_id'] ?? null;
            $paoId = $_POST['pao_id'] ?? null;

            if ($this->continuityModel->create($professorId, $paoId)) {
                $lastId = $this->continuityModel->getLastInsertedId();
                $userIdLog = $_SESSION['user_id'] ?? null;
                $newData = ['professor_id' => $professorId, 'pao_id' => $paoId, 'final_status' => 'Pendiente'];
                $this->auditLogModel->logAction($userIdLog, 'CREATE', 'continuity', $lastId, null, $newData);

                // Registrar actividad en el log de actividades
                $professorName = '';
                $professor = $this->userModel->find($professorId);
                if ($professor) $professorName = $professor['name'];
                ActivityHelper::logContinuityCreate($lastId, $professorName);

                // Crear notificación
                NotificationHelper::notifyContinuityCreate($lastId, $professorName);

                header('Location: ' . BASE_PATH . '/continuity');
                exit();
            } else {
                echo "Error al guardar la continuidad.";
            }
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $userIdLog = $_SESSION['user_id'];
        $roles = $this->roleModel->getRolesByUserId($userIdLog);
        $continuity = $this->continuityModel->find($id);

        if (!$continuity) {
            header('Location: ' . BASE_PATH . '/continuity');
            exit();
        }

        $professor = $this->userModel->find($continuity['professor_id']);
        $pao = $this->paoModel->find($continuity['pao_id']);
        $approvedBy = $this->userModel->find($continuity['docencia_decision_by']);

        // Permisos centralizados en la matriz de permisos
        $canManageAll = PermissionHelper::can('continuity', 'manage_all', $roles);
        $canEdit = PermissionHelper::can('continuity', 'edit', $roles);
        $isOwner = (int)$continuity['professor_id'] === (int)$userIdLog;

        // Verificar si TH ya tomó su decisión (esto bloquea la edición del profesor)
        $isDocenciaDecisionMade = $continuity['docencia_decision'] !== null;

        // Permiso para ver/editar la Decisión del Profesor (Sección 1)
        // El profesor solo puede editar si TH aún NO ha tomado su decisión
        $canViewEditProfessorDecision = $canManageAll || ($canEdit && $isOwner && !$isDocenciaDecisionMade);

        // Permiso para ver/editar la Decisión de Docencia/TH (Sección 2)
        $canViewEditDocenciaTHDecision = $canManageAll;

        $pageTitle = 'Editar Continuidad: ' . htmlspecialchars($continuity['id']);

        // Pasamos las nuevas variables de control a la vista
        require_once __DIR__ . '/../views/continuity/edit-continuity.php';
    }

    // =========================================================================
    // MÉTODO DE ACTUALIZACIÓN (CON RESTRICCIÓN DE WORKFLOW)
    // =========================================================================
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldContinuity = $this->continuityModel->find($id);
            if (!$oldContinuity) {
                // Redirige si la continuidad no se encuentra, en lugar de imprimir un error
                header('Location: ' . BASE_PATH . '/continuity');
                exit();
            }

            $userIdLog = $_SESSION['user_id'] ?? null;
            $roles = $this->roleModel->getRolesByUserId($userIdLog);
            $canManageAll = PermissionHelper::can('continuity', 'manage_all', $roles);
            $canEdit = PermissionHelper::can('continuity', 'edit', $roles);
            $isOwner = (int)($oldContinuity['professor_id'] ?? 0) === (int)$userIdLog;

            // Determinar qué campo se está intentando actualizar (viene del campo oculto 'update_field' de la vista)
            $fieldToUpdate = $_POST['update_field'] ?? null;
            $updated = false;
            $oldData = [];
            $newData = [];
            $decision = null;
            $success = false;
            $logAction = null;
            $approvedBy = null;

            // --- Decisión del Profesor (Primer paso) ---
            if ($fieldToUpdate === 'professor_decision' && isset($_POST['professor_decision'])) {
                if (!$canManageAll && !($canEdit && $isOwner)) {
                    header('Location: ' . BASE_PATH . '/continuity');
                    exit();
                }

                // Verificar que TH aún no haya tomado su decisión
                if ($oldContinuity['docencia_decision'] === null) {
                    $decision = (int)($_POST['professor_decision'] == '1'); // 1 o 0
                    $approvedBy = $userIdLog;

                    // Usamos el método dinámico para actualizar la decisión del profesor
                    $success = $this->continuityModel->updateDecisionDynamically(
                        $id,
                        $fieldToUpdate,
                        $decision,
                        $approvedBy
                    );

                    if ($success) {
                        $oldData = ['professor_decision' => $oldContinuity['professor_decision']];
                        $newData = ['professor_decision' => $decision];
                        $logAction = 'Decisión de Profesor';
                        $updated = true;
                    }
                }

                // --- Decisión de Docencia/TH (Segundo paso: Solo si el Profesor ya decidió) ---
            } elseif ($fieldToUpdate === 'docencia_decision' && isset($_POST['docencia_decision'])) {
                if (!$canManageAll) {
                    header('Location: ' . BASE_PATH . '/continuity');
                    exit();
                }

                // Aplicamos la restricción de workflow: solo se puede decidir si el profesor ya dio su decisión.
                if ($oldContinuity['professor_decision'] !== null) {

                    $decision = (int)($_POST['docencia_decision'] == '1'); // 1 o 0
                    $approvedBy = $userIdLog;

                    // Usamos el método dinámico para actualizar la decisión de docencia
                    $success = $this->continuityModel->updateDecisionDynamically(
                        $id,
                        $fieldToUpdate,
                        $decision,
                        $approvedBy
                    );

                    if ($success) {
                        $oldData = ['docencia_decision' => $oldContinuity['docencia_decision']];
                        $newData = ['docencia_decision' => $decision];
                        $logAction = 'Decisión de Docencia';
                        $updated = true;
                    }
                }
            }

            // Si se actualizó algo, registra el log de auditoría
            if ($updated) {
                $this->auditLogModel->logAction($userIdLog, 'UPDATE', 'continuity: ' . $logAction, $id, $oldData, $newData);
            }

            // Redirigir de vuelta a la edición para ver el cambio
            header('Location: ' . BASE_PATH . '/continuity/edit/' . $id);
            exit();
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canManageAll = PermissionHelper::can('continuity', 'manage_all', $roles);
        $canDelete = PermissionHelper::can('continuity', 'delete', $roles);

        if (!$canManageAll && !$canDelete) {
            header('Location: ' . BASE_PATH . '/continuity');
            exit();
        }

        $continuity = $this->continuityModel->find((int)$id);
        if ($continuity) {
            $isOwner = (int)($continuity['professor_id'] ?? 0) === (int)$_SESSION['user_id'];
            if (!$canManageAll && !$isOwner) {
                header('Location: ' . BASE_PATH . '/continuity');
                exit();
            }

            $query = "DELETE FROM continuity WHERE id = ?";
            $stmt = $this->continuityModel->getConnection()->prepare($query);
            $stmt->execute([(int)$id]);
            $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'continuity', (int)$id, $continuity, null);
        }

        header('Location: ' . BASE_PATH . '/continuity');
        exit();
    }
}
