<?php
// app/controllers/PortfolioController.php

require_once __DIR__ . '/../models/PortfolioModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class PortfolioController
{
    private const MAX_FILES_PER_UPLOAD = 30;
    private const MAX_FILE_SIZE_BYTES = 8388608; // 8 MB

    private $portfolioModel;
    private $userModel;
    private $paoModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->portfolioModel = new PortfolioModel();
        $this->userModel = new UserModel();
        $this->paoModel = new PaoModel();
        $this->roleModel = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    private function canManageAllPortfolios($roles)
    {
        return PermissionHelper::can('portfolios', 'manage_all', $roles);
    }

    private function uploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'supera el tamaño máximo permitido de 8 MB.';
            case UPLOAD_ERR_PARTIAL:
                return 'se subió de forma incompleta. Intente nuevamente.';
            case UPLOAD_ERR_NO_FILE:
                return 'no se recibió el archivo.';
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                return 'no se pudo guardar por un error del servidor.';
            default:
                return 'no se pudo subir (error ' . $errorCode . ').';
        }
    }

    /**
     * Procesa un grupo de archivos subidos ($_FILES[$fieldName]) validando
     * cantidad máxima (30) y tamaño máximo por archivo (8 MB).
     * Devuelve las rutas guardadas y agrega los fallos a $uploadErrors.
     */
    private function processUploadedFiles($fieldName, $uploadDir, callable $nameBuilder, array &$uploadErrors)
    {
        $uploadedFiles = [];
        if (!isset($_FILES[$fieldName]) || empty($_FILES[$fieldName]['name'][0])) {
            return $uploadedFiles;
        }

        $names = $_FILES[$fieldName]['name'];
        $selectedCount = count(array_filter($names, function ($n) {
            return $n !== null && $n !== '';
        }));
        if ($selectedCount > self::MAX_FILES_PER_UPLOAD) {
            $uploadErrors[] = 'Se seleccionaron ' . $selectedCount . ' archivos; el máximo permitido es ' .
                self::MAX_FILES_PER_UPLOAD . ' por carga. No se subió ningún archivo de este grupo.';
            return $uploadedFiles;
        }

        foreach ($names as $key => $name) {
            if ($name === null || $name === '') {
                continue;
            }
            $originalName = basename($name);
            $error = $_FILES[$fieldName]['error'][$key];
            if ($error !== UPLOAD_ERR_OK) {
                $uploadErrors[] = '"' . $originalName . '" ' . $this->uploadErrorMessage($error);
                continue;
            }
            if ((int)$_FILES[$fieldName]['size'][$key] > self::MAX_FILE_SIZE_BYTES) {
                $uploadErrors[] = '"' . $originalName . '" supera el tamaño máximo permitido de 8 MB.';
                continue;
            }
            $fileName = $nameBuilder($key, $originalName);
            if (move_uploaded_file($_FILES[$fieldName]['tmp_name'][$key], $uploadDir . $fileName)) {
                $uploadedFiles[] = '/uploads/portfolios/' . $fileName;
            } else {
                $uploadErrors[] = '"' . $originalName . '" no se pudo guardar en el servidor.';
            }
        }
        return $uploadedFiles;
    }

    private function flashUploadResult(array $uploadErrors, $uploadedCount)
    {
        if ($uploadedCount > 0) {
            $_SESSION['success_message'] = $uploadedCount . ' archivo(s) subido(s) correctamente.';
        }
        if (!empty($uploadErrors)) {
            $_SESSION['error_message'] = 'Algunos archivos NO se subieron: ' . implode(' | ', $uploadErrors);
        }
    }

    private function reportFilesOmittedByPhp(array &$uploadErrors)
    {
        $expectedCount = (int)($_POST['expected_upload_count'] ?? 0);
        if ($expectedCount <= 0) {
            return;
        }

        $receivedCount = 0;
        foreach (['docencia_files', 'practicas_files', 'titulacion_files'] as $fieldName) {
            if (!isset($_FILES[$fieldName]['name']) || !is_array($_FILES[$fieldName]['name'])) {
                continue;
            }
            foreach ($_FILES[$fieldName]['name'] as $name) {
                if ($name !== null && $name !== '') {
                    $receivedCount++;
                }
            }
        }

        if ($receivedCount < $expectedCount) {
            $uploadErrors[] = 'El navegador envió ' . $expectedCount . ' archivo(s), pero PHP solo recibió ' .
                $receivedCount . '. Revise max_file_uploads y reinicie el servidor antes de volver a cargar.';
        }
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforce('portfolios', 'view', $roles, '/dashboard');
        
        // Obtiene una lista de portafolios según el rol del usuario
        $portfolios = $this->portfolioModel->getPortfoliosWithDetails($_SESSION['user_id'], $roles);
        $pageTitle = 'Gestión de Portafolios';
        
        // Pasar información de permisos a la vista
        $canManageAll = $this->canManageAllPortfolios($roles);
        
        require_once __DIR__ . '/../views/portfolios/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforceAny('portfolios', ['create', 'manage_all'], $roles, '/portfolios');
        $professors = $this->userModel->getUsersByRole('Profesor');
        $paos = $this->paoModel->getAll();
        $pageTitle = 'Crear Nuevo Portafolio';
        require_once __DIR__ . '/../views/portfolios/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        if (!CsrfHelper::validateRequest()) {
            http_response_code(419);
            echo 'Token CSRF inválido o expirado.';
            exit();
        }

        $professorId = $_POST['professor_id'];
        $paoId = $_POST['pao_id'];
        $unitNumber = $_POST['unit_number'] ?? 1;
        $portfolioType = $_POST['portfolio_type'] ?? 'academico';

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canManageAll = $this->canManageAllPortfolios($roles);
        if (!PermissionHelper::canAny('portfolios', ['create', 'manage_all', 'manage_own'], $roles)) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }
        if (!$canManageAll && (int)$professorId !== (int)$_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        // Verificar si el portafolio ya existe para esta unidad específica
        $existing = $this->portfolioModel->findByKeys($professorId, $paoId, $unitNumber);
        if ($existing) {
            // Si ya existe, redirigir a edición (update es solo POST)
            header('Location: ' . BASE_PATH . '/portfolios/edit/' . $existing['id']);
            exit();
        }

        // Preparar datos iniciales
        $data = [
            'professor_id' => $professorId,
            'pao_id' => $paoId,
            'unit_number' => $unitNumber,
            'portfolio_type' => $portfolioType,
            'docencia_' . $portfolioType . '_path' => null,
            'practicas_' . $portfolioType . '_path' => null,
            'titulacion_' . $portfolioType . '_path' => null
        ];

        // Manejar subida de archivos según el tipo de portafolio
        $uploadDir = __DIR__ . '/../../public/uploads/portfolios/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Procesar archivo de docencia
        $uploadErrors = [];
        $totalUploaded = 0;
        $uploadedFiles = $this->processUploadedFiles('docencia_files', $uploadDir, function ($key, $originalName) use ($unitNumber) {
            return $unitNumber . '_docencia_' . time() . '_' . $key . '_' . $originalName;
        }, $uploadErrors);
        if (!empty($uploadedFiles)) {
            $totalUploaded += count($uploadedFiles);
            $data['docencia_' . $portfolioType . '_path'] = json_encode($uploadedFiles);
        }

        // Procesar archivo de prácticas (para practico y titulacion)
        if (in_array($portfolioType, ['practico', 'titulacion'])) {
            $uploadedFiles = $this->processUploadedFiles('practicas_files', $uploadDir, function ($key, $originalName) use ($unitNumber) {
                return $unitNumber . '_practicas_' . time() . '_' . $key . '_' . $originalName;
            }, $uploadErrors);
            if (!empty($uploadedFiles)) {
                $totalUploaded += count($uploadedFiles);
                $data['practicas_' . $portfolioType . '_path'] = json_encode($uploadedFiles);
            }
        }

        // Procesar archivo de titulación (solo para titulacion)
        if ($portfolioType === 'titulacion') {
            $uploadedFiles = $this->processUploadedFiles('titulacion_files', $uploadDir, function ($key, $originalName) use ($unitNumber) {
                return $unitNumber . '_titulacion_' . time() . '_' . $key . '_' . $originalName;
            }, $uploadErrors);
            if (!empty($uploadedFiles)) {
                $totalUploaded += count($uploadedFiles);
                $data['titulacion_' . $portfolioType . '_path'] = json_encode($uploadedFiles);
            }
        }

        $this->reportFilesOmittedByPhp($uploadErrors);
        $this->flashUploadResult($uploadErrors, $totalUploaded);

        // Manejar aprobación de unidad (solo para administradores)
        $canApprove = $canManageAll;

        if ($canApprove && isset($_POST['unit_approved'])) {
            $data['unit_approved'] = 1;
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->portfolioModel->create($data)) {
            $lastId = $this->portfolioModel->getLastInsertedId();
            $this->auditLogModel->logAction($_SESSION['user_id'], 'CREATE', 'portfolios', $lastId, null, $data);

            // Registrar actividad en el log de actividades
            $professorName = '';
            $professor = $this->userModel->find($professorId);
            if ($professor) $professorName = $professor['name'];
            ActivityHelper::logPortfolioCreate($lastId, $professorName);

            // Crear notificación
            NotificationHelper::notifyPortfolioCreate($lastId, $professorName);

            // Redirigir de vuelta a la página de edición del portafolio
            header('Location: ' . BASE_PATH . '/portfolios/edit/' . $lastId);
            exit();
        } else {
            $_SESSION['error_message'] = 'Error al crear el portafolio de la unidad ' . $unitNumber . '.';
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canEditAny = PermissionHelper::canAny('portfolios', ['edit', 'manage_all', 'manage_own'], $roles);
        // Los roles que pueden ver todos los portafolios acceden en modo solo lectura
        $canViewAllPortfolios = $this->portfolioModel->canViewAllPortfolios($roles);
        if (!$canEditAny && !$canViewAllPortfolios) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        $portfolio = $this->portfolioModel->find($id);
        if (!$portfolio) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        $professorId = $portfolio['professor_id'];
        $paoId = $portfolio['pao_id'];

        // Verificar que el usuario tenga permiso para editar este portafolio
        $canEditAll = $this->canManageAllPortfolios($roles);
        $isOwner = (int)$professorId === (int)$_SESSION['user_id'];
        $canModify = $canEditAll || ($canEditAny && $isOwner);

        // Si no puede modificar ni ver todos, no tiene acceso
        if (!$canModify && !$canViewAllPortfolios) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }
        $isReadOnly = !$canModify;

        // Obtener todos los datos de portafolio para este profesor y PAO
        $portfolioData = $this->portfolioModel->getPortfolio($professorId, $paoId);
        $professor = $this->userModel->find($professorId);
        $pao = $this->paoModel->find($paoId);

        if (!$professor || !$pao) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        $pageTitle = "Editar Portafolio de " . htmlspecialchars($professor['name']) . " para " . htmlspecialchars($pao['title']);

        require_once __DIR__ . '/../views/portfolios/edit.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        if (!CsrfHelper::validateRequest()) {
            http_response_code(419);
            echo 'Token CSRF inválido o expirado.';
            exit();
        }

        $oldPortfolio = $this->portfolioModel->find($id);
        if (!$oldPortfolio) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        // Verificar permisos - profesor solo puede actualizar su propio portafolio
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canEditAll = $this->canManageAllPortfolios($roles);
        if (!PermissionHelper::canAny('portfolios', ['edit', 'manage_all', 'manage_own'], $roles)) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }
        
        if (!$canEditAll && $oldPortfolio['professor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        // Verificar si la unidad está aprobada - si es así, no permitir cambios
        if ($oldPortfolio['unit_approved'] == 1) {
            $canApprove = $canEditAll;
            
            // Si no es admin o no está intentando desaprobar, rechazar cambios
            if (!$canApprove || !isset($_POST['unit_approved']) || $_POST['unit_approved'] == '1') {
                $_SESSION['error_message'] = 'Esta unidad está aprobada y no se pueden hacer cambios.';
                header('Location: ' . BASE_PATH . '/portfolios/edit/' . $id);
                exit();
            }
        }

        $unitNumber = $_POST['unit_number'];
        // IMPORTANTE: Usar el tipo del POST (el seleccionado actualmente por el usuario)
        $portfolioType = $_POST['portfolio_type'] ?? 'academico';
        
        // También actualizar el tipo en la base de datos si cambió
        if ($oldPortfolio['portfolio_type'] !== $portfolioType) {
            $this->portfolioModel->update($id, ['portfolio_type' => $portfolioType]);
        }

        // Lógica de subida de archivos múltiples
        $uploadFileDir = __DIR__ . '/../../public/uploads/portfolios/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $data = [];

        // Determinar las columnas correctas según el tipo de portafolio ACTUAL (del POST)
        $docenciaColumn = 'docencia_' . $portfolioType . '_path';
        $practicasColumn = 'practicas_' . $portfolioType . '_path';
        $titulacionColumn = 'titulacion_' . $portfolioType . '_path';

        // Leer archivos existentes de las columnas del tipo ACTUAL
        $docenciaPath = $oldPortfolio[$docenciaColumn] ?? null;
        $existingDocenciaFiles = [];
        if (!empty($docenciaPath)) {
            $decoded = json_decode($docenciaPath, true);
            $existingDocenciaFiles = is_array($decoded) ? $decoded : [$docenciaPath];
        }
        
        $uploadErrors = [];
        $totalUploaded = 0;
        // Usar el mismo formato de nombre que store() para poder mostrar la fecha de subida
        $makeFileName = function ($section) use ($unitNumber) {
            return function ($key, $originalName) use ($section, $unitNumber) {
                return $unitNumber . '_' . $section . '_' . time() . '_' . $key . '_' . $originalName;
            };
        };

        $uploadedFiles = $this->processUploadedFiles('docencia_files', $uploadFileDir, $makeFileName('docencia'), $uploadErrors);
        if (!empty($uploadedFiles)) {
            $totalUploaded += count($uploadedFiles);
            $existingDocenciaFiles = array_merge($existingDocenciaFiles, $uploadedFiles);
        }
        $data[$docenciaColumn] = !empty($existingDocenciaFiles) ? json_encode($existingDocenciaFiles) : null;

        // Procesar múltiples archivos de prácticas (solo para practico y titulacion)
        if (in_array($portfolioType, ['practico', 'titulacion'])) {
            $practicasPath = $oldPortfolio[$practicasColumn] ?? null;
            $existingPracticasFiles = [];
            if (!empty($practicasPath)) {
                $decoded = json_decode($practicasPath, true);
                $existingPracticasFiles = is_array($decoded) ? $decoded : [$practicasPath];
            }
            
            $uploadedFiles = $this->processUploadedFiles('practicas_files', $uploadFileDir, $makeFileName('practicas'), $uploadErrors);
            if (!empty($uploadedFiles)) {
                $totalUploaded += count($uploadedFiles);
                $existingPracticasFiles = array_merge($existingPracticasFiles, $uploadedFiles);
            }
            $data[$practicasColumn] = !empty($existingPracticasFiles) ? json_encode($existingPracticasFiles) : null;
        }

        // Procesar múltiples archivos de titulación (solo para titulacion)
        if ($portfolioType === 'titulacion') {
            $titulacionPath = $oldPortfolio[$titulacionColumn] ?? null;
            $existingTitulacionFiles = [];
            if (!empty($titulacionPath)) {
                $decoded = json_decode($titulacionPath, true);
                $existingTitulacionFiles = is_array($decoded) ? $decoded : [$titulacionPath];
            }
            
            $uploadedFiles = $this->processUploadedFiles('titulacion_files', $uploadFileDir, $makeFileName('titulacion'), $uploadErrors);
            if (!empty($uploadedFiles)) {
                $totalUploaded += count($uploadedFiles);
                $existingTitulacionFiles = array_merge($existingTitulacionFiles, $uploadedFiles);
            }
            $data[$titulacionColumn] = !empty($existingTitulacionFiles) ? json_encode($existingTitulacionFiles) : null;
        }

        // Manejar aprobación de unidad (solo para administradores)
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canApprove = $this->canManageAllPortfolios($roles);
        
        if ($canApprove && isset($_POST['unit_approved'])) {
            $data['unit_approved'] = $_POST['unit_approved'] == '1' ? 1 : 0;
            $data['approved_by'] = $_SESSION['user_id'];
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->portfolioModel->update($id, $data)) {
            $this->auditLogModel->logAction($_SESSION['user_id'], 'UPDATE', 'portfolios', $id, $oldPortfolio, $data);
            $this->reportFilesOmittedByPhp($uploadErrors);
            $this->flashUploadResult($uploadErrors, $totalUploaded);
            header('Location: ' . BASE_PATH . '/portfolios/edit/' . $id);
            exit();
        } else {
            $_SESSION['error_message'] = 'Error al actualizar el portafolio.';
            header('Location: ' . BASE_PATH . '/portfolios/edit/' . $id);
            exit();
        }
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
            exit();
        }

        if (!CsrfHelper::validateRequest()) {
            http_response_code(419);
            echo json_encode(['success' => false, 'message' => 'Token CSRF inválido o expirado']);
            exit();
        }

        $professorId = $_POST['professor_id'] ?? null;
        $paoId = $_POST['pao_id'] ?? null;
        $portfolioType = $_POST['portfolio_type'] ?? 'academico';

        if (!$professorId || !$paoId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canEditAll = $this->canManageAllPortfolios($roles);
        if (!PermissionHelper::canAny('portfolios', ['edit', 'manage_all'], $roles)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }
        if (!$canEditAll && (int)$professorId !== (int)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        // Actualizar el tipo de portafolio para todas las unidades de este profesor/pao
        $updated = $this->portfolioModel->updatePortfolioType($professorId, $paoId, $portfolioType);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Tipo de portafolio actualizado']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
        exit();
    }

    public function viewByProfessorPao($professorId, $paoId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        // Verificar que existan el profesor y el PAO
        $professor = $this->userModel->find($professorId);
        $pao = $this->paoModel->find($paoId);
        
        if (!$professor || !$pao) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }
        
        // Obtener el portafolio
        $portfolioData = $this->portfolioModel->getPortfolio($professorId, $paoId);
        
        $pageTitle = "Portafolio de " . htmlspecialchars($professor['name']) . " - " . htmlspecialchars($pao['name']);
        
        require_once __DIR__ . '/../views/portfolios/view.php';
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

        $portfolio = $this->portfolioModel->find((int)$id);
        if ($portfolio) {
            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
            $canDeleteAll = $this->canManageAllPortfolios($roles);
            if (!PermissionHelper::canAny('portfolios', ['delete', 'manage_all'], $roles)) {
                header('Location: ' . BASE_PATH . '/portfolios');
                exit();
            }
            if (!$canDeleteAll && (int)$portfolio['professor_id'] !== (int)$_SESSION['user_id']) {
                header('Location: ' . BASE_PATH . '/portfolios');
                exit();
            }

            $professorId = $portfolio['professor_id'];
            $paoId = $portfolio['pao_id'];

            // Obtener todos los registros del portafolio (todas las unidades)
            $query = "SELECT * FROM portfolios WHERE professor_id = ? AND pao_id = ?";
            $stmt = $this->portfolioModel->getConnection()->prepare($query);
            $stmt->execute([$professorId, $paoId]);
            $allPortfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Eliminar archivos asociados de todas las unidades
            foreach ($allPortfolios as $unit) {
                $files = ['docencia_path', 'practicas_path', 'titulacion_path'];
                foreach ($files as $field) {
                    if (!empty($unit[$field]) && file_exists(__DIR__ . '/../../public' . $unit[$field])) {
                        unlink(__DIR__ . '/../../public' . $unit[$field]);
                    }
                }
            }

            // Eliminar todos los registros del portafolio
            $deleteQuery = "DELETE FROM portfolios WHERE professor_id = ? AND pao_id = ?";
            $deleteStmt = $this->portfolioModel->getConnection()->prepare($deleteQuery);
            $deleteStmt->execute([$professorId, $paoId]);
            
            $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'portfolios', (int)$id, $portfolio, null);
        }

        header('Location: ' . BASE_PATH . '/portfolios');
        exit();
    }
}
