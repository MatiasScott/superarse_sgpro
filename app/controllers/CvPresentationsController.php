<?php
// app/controllers/CvPresentationsController.php

require_once __DIR__ . '/../models/CvPresentationsModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class CvPresentationsController
{
    private $cvPresentationsModel;
    private $auditLogModel;
    private $roleModel;

    public function __construct()
    {
        $this->cvPresentationsModel = new CvPresentationsModel();
        $this->auditLogModel = new AuditLogModel();
        $this->roleModel = new RoleModel();
    }

    private function uploadPresentationPdf()
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

        $uploadDir = __DIR__ . '/../../public/uploads/presentations/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('No se pudo preparar la carpeta para guardar el PDF.');
        }

        $fileName = bin2hex(random_bytes(16)) . '.pdf';
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            throw new RuntimeException('No se pudo guardar el PDF en el servidor.');
        }

        return '/uploads/presentations/' . $fileName;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $presentationsList = $this->cvPresentationsModel->getAllByProfessorId($_SESSION['user_id']);
        $pageTitle = 'Ponencias y Presentaciones';
        require_once __DIR__ . '/../views/professor/cv/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $eventName = $_POST['event_name'] ?? '';
            $institutionName = $_POST['institution_name'] ?? '';
            $year = $_POST['year'] ?? '';
            $presentation = $_POST['presentation'] ?? '';

            try {
                $archivoPdf = $this->uploadPresentationPdf() ?? '';
            } catch (RuntimeException $error) {
                http_response_code(400);
                echo htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8');
                return;
            }

            try {
                $created = $this->cvPresentationsModel->create(
                    $professorId,
                    $eventName,
                    $institutionName,
                    $year,
                    $presentation,
                    $archivoPdf
                );
            } catch (PDOException $error) {
                error_log('CvPresentationsController::store database error: ' . $error->getMessage());
                if ($archivoPdf !== '') {
                    $uploadedFilePath = __DIR__ . '/../../public' . $archivoPdf;
                    if (is_file($uploadedFilePath) && !unlink($uploadedFilePath)) {
                        error_log('CvPresentationsController::store could not remove the uploaded PDF after a database error.');
                    }
                }
                http_response_code(500);
                echo 'No se pudo guardar la ponencia. Verifique la configuración de base de datos y vuelva a intentarlo.';
                return;
            }

            if ($created) {
                $lastId = $this->cvPresentationsModel->getLastInsertedId();
                $this->auditLogModel->logAction($professorId, 'CREATE', 'cv_presentations', $lastId);
                header('Location: ' . BASE_PATH . '/professor/cv');
                exit();
            } else {
                if ($archivoPdf !== '') {
                    $uploadedFilePath = __DIR__ . '/../../public' . $archivoPdf;
                    if (is_file($uploadedFilePath) && !unlink($uploadedFilePath)) {
                        error_log('CvPresentationsController::store could not remove the uploaded PDF after a failed insert.');
                    }
                }
                echo "Error al guardar la presentación.";
            }
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professorId = $_SESSION['user_id'] ?? null;
            $eventName = $_POST['event_name'] ?? '';
            $institutionName = $_POST['institution_name'] ?? '';
            $year = $_POST['year'] ?? '';
            $presentation = $_POST['presentation'] ?? '';

            $existingPresentation = $this->cvPresentationsModel->findById($id);
            if (!$existingPresentation) {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro de la ponencia']);
                exit();
            }

            try {
                $uploadedPdf = $this->uploadPresentationPdf();
            } catch (RuntimeException $error) {
                echo json_encode(['success' => false, 'message' => $error->getMessage()]);
                exit();
            }

            $archivoPdf = $uploadedPdf ?? ($existingPresentation['archivo_pdf'] ?? '');

            try {
                $updated = $this->cvPresentationsModel->update(
                    $id,
                    $eventName,
                    $institutionName,
                    $year,
                    $presentation,
                    $archivoPdf
                );
            } catch (PDOException $error) {
                error_log('CvPresentationsController::update database error: ' . $error->getMessage());
                if ($uploadedPdf !== null) {
                    $uploadedFilePath = __DIR__ . '/../../public' . $uploadedPdf;
                    if (is_file($uploadedFilePath) && !unlink($uploadedFilePath)) {
                        error_log('CvPresentationsController::update could not remove the uploaded PDF after a database error.');
                    }
                }
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la ponencia.']);
                exit();
            }

            if ($updated) {
                $this->auditLogModel->logAction($professorId, 'UPDATE', 'cv_presentations', $id);
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            } else {
                if ($uploadedPdf !== null) {
                    $uploadedFilePath = __DIR__ . '/../../public' . $uploadedPdf;
                    if (is_file($uploadedFilePath) && !unlink($uploadedFilePath)) {
                        error_log('CvPresentationsController::update could not remove the uploaded PDF after a failed update.');
                    }
                }
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

        if ($this->cvPresentationsModel->delete($id)) {
            $this->auditLogModel->logAction($professorId, 'DELETE', 'cv_presentations', $id);
        }

        header('Location: ' . BASE_PATH . '/professor/cv');
        exit();
    }
}