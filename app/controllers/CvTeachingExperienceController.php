<?php
// app/controllers/CvTeachingExperienceController.php

require_once __DIR__ . '/../models/CvTeachingExperienceModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvTeachingExperienceController
{
    private $cvTeachingExperienceModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvTeachingExperienceModel = new CvTeachingExperienceModel();
        $this->auditLogModel = new AuditLogModel();
        $this->roleModel = new RoleModel();
    }

    private function uploadTeachingExperiencePdf()
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

        $uploadDir = __DIR__ . '/../../public/uploads/teaching/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('No se pudo preparar la carpeta para guardar el PDF.');
        }

        $fileName = bin2hex(random_bytes(16)) . '.pdf';
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            throw new RuntimeException('No se pudo guardar el PDF en el servidor.');
        }

        return '/uploads/teaching/' . $fileName;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $teachingExperienceList = $this->cvTeachingExperienceModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Experiencia en Docencia';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $ies = $_POST['ies'] ?? '';
            $denomination = $_POST['denomination'] ?? '';
            $subjects = $_POST['subjects'] ?? '';

            try {
                $archivoPDF = $this->uploadTeachingExperiencePdf() ?? '';
            } catch (RuntimeException $error) {
                http_response_code(400);
                echo htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8');
                return;
            }

            try {
                $created = $this->cvTeachingExperienceModel->create(
                    $professorId,
                    $startDate,
                    $endDate,
                    $ies,
                    $denomination,
                    $subjects,
                    $archivoPDF
                );
            } catch (PDOException $error) {
                error_log('CvTeachingExperienceController::store database error: ' . $error->getMessage());
                if ($archivoPDF !== '') {
                    $uploadedFilePath = __DIR__ . '/../../public' . $archivoPDF;
                    if (is_file($uploadedFilePath) && !unlink($uploadedFilePath)) {
                        error_log('CvTeachingExperienceController::store could not remove the uploaded PDF after a database error.');
                    }
                }
                http_response_code(500);
                echo 'No se pudo guardar la experiencia docente. Verifique la configuración de base de datos y vuelva a intentarlo.';
                return;
            }

            if ($created) {
                $lastId = $this->cvTeachingExperienceModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_teaching_experience', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                echo "Error al guardar la experiencia en docencia.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $ies = $_POST['ies'] ?? '';
            $denomination = $_POST['denomination'] ?? '';
            $subjects = $_POST['subjects'] ?? '';

            if ($this->cvTeachingExperienceModel->update(
                $id,
                $startDate,
                $endDate,
                $ies,
                $denomination,
                $subjects
            )) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_teaching_experience', $id);
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

        if ($this->cvTeachingExperienceModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_teaching_experience', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}