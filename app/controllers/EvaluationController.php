<?php
// app/controllers/EvaluationController.php

require_once __DIR__ . '/../models/EvaluationModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class EvaluationController
{
    private $evaluationModel;
    private $userModel;
    private $paoModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->evaluationModel = new EvaluationModel();
        $this->userModel = new UserModel();
        $this->paoModel = new PaoModel();
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * Función auxiliar para verificar si el usuario tiene uno de los roles dados.
     * @param array $userRoles Roles del usuario logueado (ej: [['role_name' => 'Professor']]).
     * @param array $allowedRoles Lista de roles permitidos (ej: ['Super Administrador', 'Coordinador académico']).
     * @return bool
     */
    private function hasRole(array $userRoles, array $allowedRoles): bool
    {
        $allowed = array_map('strtolower', $allowedRoles);
        $allowed = array_map(function($role) {
            return trim(str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], strtolower($role)));
        }, $allowedRoles);
        
        foreach ($userRoles as $role) {
            if (isset($role['role_name'])) {
                $userRoleName = trim(str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], strtolower($role['role_name'])));
                if (in_array($userRoleName, $allowed)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Redirige a la página de inicio de sesión si el usuario no está autenticado, 
     * o a una página de error de acceso si no está autorizado.
     */
    private function denyAccess()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/'); // No logueado, redirigir al login
        } else {
            // Logueado, pero sin permisos para la acción
            http_response_code(403);
            die("Acceso denegado: No tiene permisos para realizar esta acción.");
        }
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
        PermissionHelper::enforce('evaluations', 'view', $roles, '/dashboard');

        // Obtener evaluaciones filtradas según el rol del usuario
        try {
            $evaluations = $this->evaluationModel->getEvaluationsWithDetails($userId, $roles);
            error_log("Evaluaciones encontradas: " . count($evaluations));
        } catch (Exception $e) {
            error_log("Error al obtener evaluaciones: " . $e->getMessage());
            $evaluations = [];
        }
        
        $pageTitle = 'Gestión de Evaluaciones';
        require_once __DIR__ . '/../views/evaluations/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('evaluations', 'create', $roles, '/evaluations');

        // Profesores: solo usuarios con rol Profesor
        $professors = $this->userModel->getUsersByRole('Profesor');
        // Evaluadores: se mantienen todos los usuarios como estaba originalmente
        $evaluators = $this->userModel->getAll();
        
        $paos = $this->paoModel->getAll();
        error_log("DEBUG EvaluationController::edit - PAOs count: " . count($paos));
        error_log("DEBUG EvaluationController::edit - PAOs data: " . print_r($paos, true));
        $pageTitle = 'Crear Evaluación';
        require_once __DIR__ . '/../views/evaluations/create.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('evaluations', 'create', $roles, '/evaluations');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mostrar datos recibidos para debug
            error_log("POST data: " . print_r($_POST, true));
            
            $data = [
                'professor_id' => $_POST['professor_id'] ?? null,
                'pao_id' => $_POST['pao_id'] ?? null,
                'evaluator_id' => $_POST['evaluator_id'] ?? null,
                'autoevaluacion' => $_POST['autoevaluacion'] ?? 0,
                'coevaluacion_20' => $_POST['coevaluacion_20'] ?? 0,
                'coevaluacion_30' => $_POST['coevaluacion_30'] ?? 0,
                'coevaluacion' => $_POST['coevaluacion'] ?? 0,
                'heteroevaluacion' => $_POST['heteroevaluacion'] ?? 0,
                'score' => $_POST['score'] ?? null,
                'comments' => $_POST['comments'] ?? '',
                'initial_file_path' => null,
                'status' => 'Pendiente de subida',
                'final_status' => 'En proceso'
            ];

            // Validar datos requeridos
            if (empty($data['professor_id']) || empty($data['pao_id']) || empty($data['evaluator_id']) || empty($data['score'])) {
                $missing = [];
                if (empty($data['professor_id'])) $missing[] = 'Profesor';
                if (empty($data['pao_id'])) $missing[] = 'PAO';
                if (empty($data['evaluator_id'])) $missing[] = 'Evaluador';
                if (empty($data['score'])) $missing[] = 'Puntaje';

                $_SESSION['flash_error'] = 'Faltan campos requeridos: ' . implode(', ', $missing);
                header('Location: ' . BASE_PATH . '/evaluations/create');
                exit();
            }

            // Validar que el profesor seleccionado sí tenga el rol Profesor
            $selectedProfessorRoles = $this->roleModel->getRolesByUserId((int) $data['professor_id']);
            if (!$this->hasRole($selectedProfessorRoles, ['Profesor'])) {
                $_SESSION['flash_error'] = 'El usuario seleccionado no tiene rol de Profesor.';
                header('Location: ' . BASE_PATH . '/evaluations/create');
                exit();
            }

            if (isset($_FILES['initial_file']) && $_FILES['initial_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['initial_file']['tmp_name'];
                $fileName = uniqid() . '_' . basename($_FILES['initial_file']['name']);
                $uploadFileDir = __DIR__ . '/../../public/uploads/evaluations/';
                $destPath = $uploadFileDir . $fileName;

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data['initial_file_path'] = rtrim(BASE_PATH, '/') . '/uploads/evaluations/' . $fileName;
                    $data['status'] = 'Pendiente de firma';
                }
            }

            try {
                error_log("Intentando crear evaluación con datos: " . print_r($data, true));
                
                $result = $this->evaluationModel->create($data);
                error_log("Resultado del create: " . ($result ? 'true' : 'false'));
                
                if ($result) {
                    $lastId = $this->evaluationModel->getLastInsertedId();
                    error_log("Last ID: " . $lastId);
                    
                    // Registrar en audit log (opcional)
                    try {
                        $this->auditLogModel->logAction($_SESSION['user_id'], 'CREATE', 'evaluations', $lastId, null, $data);
                    } catch (Exception $e) {
                        error_log("Error en audit log: " . $e->getMessage());
                    }

                    // Registrar actividad (opcional)
                    try {
                        $professorName = '';
                        $professor = $this->userModel->find($data['professor_id']);
                        if ($professor) $professorName = $professor['name'];
                        ActivityHelper::logEvaluationCreate($lastId, $professorName);
                        NotificationHelper::notifyEvaluationCreate($lastId, $professorName);
                    } catch (Exception $e) {
                        error_log("Error en notificaciones: " . $e->getMessage());
                    }

                    $_SESSION['flash_success'] = 'Evaluación creada exitosamente';
                    header('Location: ' . BASE_PATH . '/evaluations');
                    exit();
                } else {
                    $errorInfo = $this->evaluationModel->getConnection()->errorInfo();
                    error_log("Error PDO: " . print_r($errorInfo, true));
                    $_SESSION['flash_error'] = 'Error al guardar: ' . ($errorInfo[2] ?? 'Error desconocido');
                    header('Location: ' . BASE_PATH . '/evaluations/create');
                    exit();
                }
            } catch (Exception $e) {
                error_log("Exception: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
                header('Location: ' . BASE_PATH . '/evaluations/create');
                exit();
            }
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            $this->denyAccess();
        }

        $userId = $_SESSION['user_id'];
        $roles = $this->roleModel->getRolesByUserId($userId);
        $evaluation = $this->evaluationModel->find((int)$id);

        if (!$evaluation) {
            // Si la evaluación no existe, redirigir
            header('Location: ' . BASE_PATH . '/evaluations');
            exit();
        }

        $canManageAll = PermissionHelper::can('evaluations', 'manage_all', $roles);
        $canEditAction = PermissionHelper::can('evaluations', 'edit', $roles);
        $isProfessor = $this->hasRole($roles, ['Profesor']);
        $isOwnerProfessor = $isProfessor && (int)$evaluation['professor_id'] === (int)$userId;
        $canEditRecord = $canManageAll || ($canEditAction && (!$isProfessor || $isOwnerProfessor));

        if (!$canEditRecord) {
            $this->denyAccess();
        }

        $professors = $this->userModel->getUsersByRole('Profesor');
        // Intentar obtener evaluadores por rol. Si la consulta con acento falla, intentar sin acento, luego fallback a todos.
        $evaluators = $this->userModel->getUsersByRole('Coordinador académico');
        if (empty($evaluators)) {
            error_log("WARN EvaluationController::edit - No evaluators found for role 'Coordinador académico', trying without accent");
            $evaluators = $this->userModel->getUsersByRole('Coordinador academico');
        }
        if (empty($evaluators)) {
            error_log("WARN EvaluationController::edit - No evaluators found by role, falling back to getAll()");
            $evaluators = $this->userModel->getAll();
        }
        $paos = $this->paoModel->getAll();
        error_log("DEBUG EvaluationController::edit - PAOs count (initial): " . count($paos));
        error_log("DEBUG EvaluationController::edit - PAOs data (initial): " . print_r(array_slice($paos, 0, 10), true));

        // Si no se obtuvo ningún PAO, intentar una consulta directa por seguridad
        if (empty($paos)) {
            try {
                $conn = $this->paoModel->getConnection();
                $stmt = $conn->prepare("SELECT id, title, name FROM pao ORDER BY id DESC LIMIT 100");
                $stmt->execute();
                $rawPaos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rawPaos)) {
                    $paos = $rawPaos;
                    error_log("DEBUG EvaluationController::edit - PAOs fetched via raw query: " . count($paos));
                    error_log("DEBUG EvaluationController::edit - PAOs data (raw): " . print_r(array_slice($paos, 0, 10), true));
                } else {
                    error_log("WARN EvaluationController::edit - Raw query returned 0 PAOs");
                }
            } catch (Exception $e) {
                error_log("ERROR EvaluationController::edit - Exception fetching PAOs raw: " . $e->getMessage());
            }
        }

        $pageTitle = 'Editar Evaluación: ' . htmlspecialchars($evaluation['id']);
        require_once __DIR__ . '/../views/evaluations/edit.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            $this->denyAccess();
        }

        $userId = $_SESSION['user_id'];
        $roles = $this->roleModel->getRolesByUserId($userId);
        $oldEvaluation = $this->evaluationModel->find((int)$id);

        if (!$oldEvaluation) {
            header('Location: ' . BASE_PATH . '/evaluations');
            exit();
        }

        $canManageAll = PermissionHelper::can('evaluations', 'manage_all', $roles);
        $canEditAction = PermissionHelper::can('evaluations', 'edit', $roles);
        $isProfessor = $this->hasRole($roles, ['Profesor']);
        $isOwnerProfessor = $isProfessor && (int)$oldEvaluation['professor_id'] === (int)$userId;
        $canEditRecord = $canManageAll || ($canEditAction && (!$isProfessor || $isOwnerProfessor));

        if (!$canEditRecord) {
            $this->denyAccess();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newComment = trim($_POST['new_comment'] ?? '');
            $existingComments = (string)($oldEvaluation['comments'] ?? '');
            $finalComments = $existingComments;

            if ($newComment !== '') {
                $currentUser = $this->userModel->find($userId);
                $authorName = $currentUser['name'] ?? ('Usuario #' . $userId);
                $commentEntry = '[' . date('Y-m-d H:i') . '] ' . $authorName . ': ' . $newComment;
                $finalComments = $existingComments !== ''
                    ? ($existingComments . PHP_EOL . PHP_EOL . $commentEntry)
                    : $commentEntry;
            }

            $data = [
                'professor_id' => $_POST['professor_id'] ?? null,
                'pao_id' => $_POST['pao_id'] ?? null,
                'evaluator_id' => $_POST['evaluator_id'] ?? null,
                'autoevaluacion' => $_POST['autoevaluacion'] ?? 0,
                'coevaluacion_20' => $_POST['coevaluacion_20'] ?? 0,
                'coevaluacion_30' => $_POST['coevaluacion_30'] ?? 0,
                'coevaluacion' => $_POST['coevaluacion'] ?? 0,
                'heteroevaluacion' => $_POST['heteroevaluacion'] ?? 0,
                'score' => $_POST['score'] ?? null,
                'comments' => $finalComments,
                'status' => $_POST['status'] ?? 'Pendiente de subida',
                'final_status' => $_POST['final_status'] ?? 'En proceso'
            ];

            // Restricción docente sin manage_all: solo puede agregar comentarios.
            if ($isProfessor && !$canManageAll) {
                $data['professor_id'] = $oldEvaluation['professor_id'];
                $data['pao_id'] = $oldEvaluation['pao_id'];
                $data['evaluator_id'] = $oldEvaluation['evaluator_id'];
                $data['autoevaluacion'] = $oldEvaluation['autoevaluacion'];
                $data['coevaluacion_20'] = $oldEvaluation['coevaluacion_20'] ?? 0;
                $data['coevaluacion_30'] = $oldEvaluation['coevaluacion_30'] ?? 0;
                $data['coevaluacion'] = $oldEvaluation['coevaluacion'];
                $data['heteroevaluacion'] = $oldEvaluation['heteroevaluacion'];
                $data['score'] = $oldEvaluation['score'];
                $data['status'] = $oldEvaluation['status'];
                $data['final_status'] = $oldEvaluation['final_status'];
            }
            
            // Mantener archivos existentes si no se suben nuevos
            if (isset($oldEvaluation['initial_file_path'])) {
                $data['initial_file_path'] = $oldEvaluation['initial_file_path'];
            }
            if (isset($oldEvaluation['signed_file_path'])) {
                $data['signed_file_path'] = $oldEvaluation['signed_file_path'];
            }

            // Lógica para subir un nuevo archivo si se proporciona (no aplica a Profesor)
            if (!$isProfessor && isset($_FILES['initial_file']) && $_FILES['initial_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['initial_file']['tmp_name'];
                $fileName = uniqid() . '_' . basename($_FILES['initial_file']['name']);
                $uploadFileDir = __DIR__ . '/../../public/uploads/evaluations/';
                $destPath = $uploadFileDir . $fileName;

                // Nota: Verifiqué y corregí la ruta de uploadFileDir en el controlador.
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data['initial_file_path'] = rtrim(BASE_PATH, '/') . '/uploads/evaluations/' . $fileName;
                }
            }

            // Lógica para subir un nuevo archivo firmado si se proporciona (no aplica a Profesor)
            if (!$isProfessor && isset($_FILES['signed_file']) && $_FILES['signed_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['signed_file']['tmp_name'];
                $fileName = uniqid() . '_signed_' . basename($_FILES['signed_file']['name']);
                $uploadFileDir = __DIR__ . '/../../public/uploads/evaluations/';
                $destPath = $uploadFileDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data['signed_file_path'] = rtrim(BASE_PATH, '/') . '/uploads/evaluations/' . $fileName;
                    $data['professor_signed_at'] = date('Y-m-d H:i:s');
                }
            }

            if ($this->evaluationModel->update((int)$id, $data)) {
                $this->auditLogModel->logAction($userId, 'UPDATE', 'evaluations', (int)$id, $oldEvaluation, $data);
                header('Location: ' . BASE_PATH . '/evaluations');
                exit();
            } else {
                echo "Error al actualizar la evaluación.";
            }
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canManageAll = PermissionHelper::can('evaluations', 'manage_all', $roles);
        $canDeleteAction = PermissionHelper::can('evaluations', 'delete', $roles);
        if (!$canManageAll && !$canDeleteAction) {
            $this->denyAccess();
        }

        $evaluation = $this->evaluationModel->find((int)$id);
        if (!$evaluation) {
            header('Location: ' . BASE_PATH . '/evaluations');
            exit();
        }

        if (!$canManageAll && (int)$evaluation['professor_id'] !== (int)$_SESSION['user_id']) {
            $this->denyAccess();
        }

        // Eliminar archivos asociados si existen
        if (!empty($evaluation['initial_file_path']) && file_exists(__DIR__ . '/../../public' . $evaluation['initial_file_path'])) {
            unlink(__DIR__ . '/../../public' . $evaluation['initial_file_path']);
        }
        if (!empty($evaluation['signed_file_path']) && file_exists(__DIR__ . '/../../public' . $evaluation['signed_file_path'])) {
            unlink(__DIR__ . '/../../public' . $evaluation['signed_file_path']);
        }

        // Eliminar el registro
        $query = "DELETE FROM evaluations WHERE id = ?";
        $stmt = $this->evaluationModel->getConnection()->prepare($query);
        
        if ($stmt->execute([(int)$id])) {
            $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'evaluations', (int)$id, $evaluation, null);
            header('Location: ' . BASE_PATH . '/evaluations');
            exit();
        } else {
            echo "Error al eliminar la evaluación.";
        }
    }
}
