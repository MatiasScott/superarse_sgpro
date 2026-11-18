<?php
// app/controllers/CvEducationController.php

require_once __DIR__ . '/../models/CvEducationModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvEducationController
{
    private $cvEducationModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvEducationModel = new CvEducationModel();
        $this->auditLogModel = new AuditLogModel();
        $this->roleModel = new RoleModel();
    }

    private function uploadEducationPdf()
    {
        if (!isset($_FILES['archivo_pdf']) || $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES['archivo_pdf'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo recibir el PDF. Intente cargarlo nuevamente.');
        }

        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            throw new RuntimeException('El archivo debe tener formato PDF.');
        }

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        if ($fileInfo->file($file['tmp_name']) !== 'application/pdf') {
            throw new RuntimeException('El archivo seleccionado no es un PDF válido.');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/education/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('No se pudo preparar la carpeta para guardar el PDF.');
        }

        $fileName = bin2hex(random_bytes(16)) . '.pdf';
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            throw new RuntimeException('No se pudo guardar el PDF en el servidor.');
        }

        return '/uploads/education/' . $fileName;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $educationList = $this->cvEducationModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Educación y Formación';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $educationLevel = $_POST['education_level'] ?? '';
            $institutionName = $_POST['institution_name'] ?? '';
            $degreeTitle = $_POST['degree_title'] ?? '';
            $senescytRegister = $_POST['senescyt_register'] ?? '';
            try {
                $archivoPDF = $this->uploadEducationPdf() ?? '';
            } catch (RuntimeException $error) {
                http_response_code(400);
                echo htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8');
                return;
            }

            try {
                $created = $this->cvEducationModel->create(
                    $professorId,
                    $educationLevel,
                    $institutionName,
                    $degreeTitle,
                    $senescytRegister,
                    $archivoPDF
                );
            } catch (PDOException $error) {
                error_log('CvEducationController::store database error: ' . $error->getMessage());
                if ($archivoPDF !== '') {
                    $uploadedFilePath = __DIR__ . '/../../public' . $archivoPDF;
                    if (is_file($uploadedFilePath) && !unlink($uploadedFilePath)) {
                        error_log('CvEducationController::store could not remove the uploaded PDF after a database error.');
                    }
                }
                http_response_code(500);
                echo 'No se pudo guardar el grado. Verifique la configuración de base de datos y vuelva a intentarlo.';
                return;
            }

            if ($created) {
                $lastId = $this->cvEducationModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_education', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la información de educación.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $educationLevel = $_POST['education_level'] ?? '';
            $institutionName = $_POST['institution_name'] ?? '';
            $degreeTitle = $_POST['degree_title'] ?? '';
            $senescytRegister = $_POST['senescyt_register'] ?? '';
            try {
                $uploadedPdf = $this->uploadEducationPdf();
            } catch (RuntimeException $error) {
                echo json_encode(['success' => false, 'message' => $error->getMessage()]);
                exit();
            }

            $education = $this->cvEducationModel->findById($id);
            if (!$education) {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro de educación']);
                exit();
            }
            $archivoPDF = $uploadedPdf ?? ($education['archivo_pdf'] ?? '');

            if ($this->cvEducationModel->update(
                $id,
                $educationLevel,
                $institutionName,
                $degreeTitle,
                $senescytRegister,
                $archivoPDF
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_education', $id);
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
        exit();
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $professorId = $_SESSION['user_id'];

        if ($this->cvEducationModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_education', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}