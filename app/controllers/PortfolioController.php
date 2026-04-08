<?php
// app/controllers/PortfolioController.php

require_once __DIR__ . '/../models/PortfolioModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';

class PortfolioController
{
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

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($roles, 'role_name');
        
        // Obtiene una lista de portafolios según el rol del usuario
        $portfolios = $this->portfolioModel->getPortfoliosWithDetails($_SESSION['user_id'], $roles);
        $pageTitle = 'Gestión de Portafolios';
        
        // Pasar información de permisos a la vista
        $canManageAll = in_array('Super Administrador', $roleNames) || 
                       in_array('Director de docencia', $roleNames) ||
                       in_array('Coordinador académico', $roleNames);
        
        require_once __DIR__ . '/../views/portfolios/index.php';
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
        $pageTitle = 'Crear Nuevo Portafolio';
        require_once __DIR__ . '/../views/portfolios/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        $professorId = $_POST['professor_id'];
        $paoId = $_POST['pao_id'];
        $unitNumber = $_POST['unit_number'] ?? 1;
        $portfolioType = $_POST['portfolio_type'] ?? 'academico';

        // Verificar si el portafolio ya existe para esta unidad específica
        $existing = $this->portfolioModel->findByKeys($professorId, $paoId, $unitNumber);
        if ($existing) {
            // Si ya existe, redirigir al update
            header('Location: ' . BASE_PATH . '/portfolios/update/' . $existing['id']);
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
        if (isset($_FILES['docencia_files']) && !empty($_FILES['docencia_files']['name'][0])) {
            $uploadedFiles = [];
            foreach ($_FILES['docencia_files']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $originalName = basename($_FILES['docencia_files']['name'][$key]);
                    $uniqueName = $unitNumber . '_docencia_' . time() . '_' . $key . '_' . $originalName;
                    $uploadPath = $uploadDir . $uniqueName;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $uploadedFiles[] = '/uploads/portfolios/' . $uniqueName;
                    }
                }
            }
            if (!empty($uploadedFiles)) {
                $data['docencia_' . $portfolioType . '_path'] = json_encode($uploadedFiles);
            }
        }

        // Procesar archivo de prácticas (para practico y titulacion)
        if (in_array($portfolioType, ['practico', 'titulacion']) && 
            isset($_FILES['practicas_files']) && !empty($_FILES['practicas_files']['name'][0])) {
            $uploadedFiles = [];
            foreach ($_FILES['practicas_files']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $originalName = basename($_FILES['practicas_files']['name'][$key]);
                    $uniqueName = $unitNumber . '_practicas_' . time() . '_' . $key . '_' . $originalName;
                    $uploadPath = $uploadDir . $uniqueName;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $uploadedFiles[] = '/uploads/portfolios/' . $uniqueName;
                    }
                }
            }
            if (!empty($uploadedFiles)) {
                $data['practicas_' . $portfolioType . '_path'] = json_encode($uploadedFiles);
            }
        }

        // Procesar archivo de titulación (solo para titulacion)
        if ($portfolioType === 'titulacion' && 
            isset($_FILES['titulacion_files']) && !empty($_FILES['titulacion_files']['name'][0])) {
            $uploadedFiles = [];
            foreach ($_FILES['titulacion_files']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $originalName = basename($_FILES['titulacion_files']['name'][$key]);
                    $uniqueName = $unitNumber . '_titulacion_' . time() . '_' . $key . '_' . $originalName;
                    $uploadPath = $uploadDir . $uniqueName;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $uploadedFiles[] = '/uploads/portfolios/' . $uniqueName;
                    }
                }
            }
            if (!empty($uploadedFiles)) {
                $data['titulacion_' . $portfolioType . '_path'] = json_encode($uploadedFiles);
            }
        }

        // Manejar aprobación de unidad (solo para administradores)
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($roles, 'role_name');
        $canApprove = in_array('Super Administrador', $roleNames) || 
                     in_array('Director de docencia', $roleNames) ||
                     in_array('Coordinador académico', $roleNames);

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
        $roleNames = array_column($roles, 'role_name');

        $portfolio = $this->portfolioModel->find($id);
        if (!$portfolio) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        $professorId = $portfolio['professor_id'];
        $paoId = $portfolio['pao_id'];

        // Verificar que el usuario tenga permiso para editar este portafolio
        $canEditAll = in_array('Super Administrador', $roleNames) || 
                     in_array('Director de docencia', $roleNames) ||
                     in_array('Coordinador académico', $roleNames);
        
        // Si es profesor, solo puede editar su propio portafolio
        if (!$canEditAll && $professorId != $_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

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

        $oldPortfolio = $this->portfolioModel->find($id);
        if (!$oldPortfolio) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        // Verificar permisos - profesor solo puede actualizar su propio portafolio
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($roles, 'role_name');
        $canEditAll = in_array('Super Administrador', $roleNames) || 
                     in_array('Director de docencia', $roleNames) ||
                     in_array('Coordinador académico', $roleNames);
        
        if (!$canEditAll && $oldPortfolio['professor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/portfolios');
            exit();
        }

        // Verificar si la unidad está aprobada - si es así, no permitir cambios
        if ($oldPortfolio['unit_approved'] == 1) {
            $canApprove = in_array('Super Administrador', $roleNames) || 
                         in_array('Director de docencia', $roleNames) ||
                         in_array('Coordinador académico', $roleNames);
            
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
        
        if (isset($_FILES['docencia_files']) && !empty($_FILES['docencia_files']['name'][0])) {
            $uploadedFiles = [];
            
            foreach ($_FILES['docencia_files']['name'] as $key => $name) {
                if ($_FILES['docencia_files']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($name);
                    if (move_uploaded_file($_FILES['docencia_files']['tmp_name'][$key], $uploadFileDir . $fileName)) {
                        $uploadedFiles[] = '/uploads/portfolios/' . $fileName;
                    }
                }
            }
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
            
            if (isset($_FILES['practicas_files']) && !empty($_FILES['practicas_files']['name'][0])) {
                $uploadedFiles = [];
                
                foreach ($_FILES['practicas_files']['name'] as $key => $name) {
                    if ($_FILES['practicas_files']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = uniqid() . '_' . basename($name);
                        if (move_uploaded_file($_FILES['practicas_files']['tmp_name'][$key], $uploadFileDir . $fileName)) {
                            $uploadedFiles[] = '/uploads/portfolios/' . $fileName;
                        }
                    }
                }
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
            
            if (isset($_FILES['titulacion_files']) && !empty($_FILES['titulacion_files']['name'][0])) {
                $uploadedFiles = [];
                
                foreach ($_FILES['titulacion_files']['name'] as $key => $name) {
                    if ($_FILES['titulacion_files']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = uniqid() . '_' . basename($name);
                        if (move_uploaded_file($_FILES['titulacion_files']['tmp_name'][$key], $uploadFileDir . $fileName)) {
                            $uploadedFiles[] = '/uploads/portfolios/' . $fileName;
                        }
                    }
                }
                $existingTitulacionFiles = array_merge($existingTitulacionFiles, $uploadedFiles);
            }
            $data[$titulacionColumn] = !empty($existingTitulacionFiles) ? json_encode($existingTitulacionFiles) : null;
        }

        // Manejar aprobación de unidad (solo para administradores)
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $roleNames = array_column($roles, 'role_name');
        $canApprove = in_array('Super Administrador', $roleNames) || 
                     in_array('Director de docencia', $roleNames) ||
                     in_array('Coordinador académico', $roleNames);
        
        if ($canApprove && isset($_POST['unit_approved'])) {
            $data['unit_approved'] = $_POST['unit_approved'] == '1' ? 1 : 0;
            $data['approved_by'] = $_SESSION['user_id'];
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->portfolioModel->update($id, $data)) {
            $this->auditLogModel->logAction($_SESSION['user_id'], 'UPDATE', 'portfolios', $id, $oldPortfolio, $data);
            header('Location: ' . BASE_PATH . '/portfolios/edit/' . $id);
            exit();
        } else {
            echo "Error al actualizar el portafolio.";
        }
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
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

        $portfolio = $this->portfolioModel->find((int)$id);
        if ($portfolio) {
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

