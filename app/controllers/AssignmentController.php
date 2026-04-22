<?php
// app/controllers/AssignmentController.php

require_once __DIR__ . '/../models/AssignmentModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SubjectModel.php';
require_once __DIR__ . '/../models/PaoModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/AuditLogModel.php';
require_once __DIR__ . '/../helpers/ActivityHelper.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class AssignmentController
{
    private $assignmentModel;
    private $userModel;
    private $subjectModel;
    private $paoModel;
    private $roleModel;
    private $auditLogModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->userModel = new UserModel();
        $this->subjectModel = new SubjectModel();
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
        $assignments = $this->assignmentModel->getAssignmentsWithDetails($userId, $roles);
        $pageTitle = 'Gestión de Asignaciones';
        // Ruta de la vista actualizada
        require_once __DIR__ . '/../views/academic/assignments.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        PermissionHelper::enforceAny('assignments', ['create', 'manage_all'], $roles, '/academic/assignments');
        $professors = $this->userModel->getUsersByRole('Profesor');
        $subjects = $this->subjectModel->getAll();
        $paos = $this->paoModel->getAll();
        $pageTitle = 'Crear Nueva Asignación';
        // Ruta de la vista actualizada
        require_once __DIR__ . '/../views/academic/create-assignments.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id'] ?? 0);
            PermissionHelper::enforceAny('assignments', ['create', 'manage_all'], $roles, '/academic/assignments');

            $professorId = $_POST['professor_id'] ?? null;
            $subjectId = $_POST['subject_id'] ?? null;
            $paoId = $_POST['pao_id'] ?? null;
            $hoursPerWeek = isset($_POST['hours_per_week']) ? (float)$_POST['hours_per_week'] : null;
            $status = $_POST['status'] ?? 'Asignado';

            if ($this->assignmentModel->create($professorId, $subjectId, $paoId, $hoursPerWeek, $status)) {
                $lastAssignmentId = $this->assignmentModel->getLastInsertedId();
                $userIdLog = $_SESSION['user_id'] ?? null;
                $newData = ['professor_id' => $professorId, 'subject_id' => $subjectId, 'pao_id' => $paoId, 'hours_per_week' => $hoursPerWeek, 'status' => $status];
                $this->auditLogModel->logAction($userIdLog, 'CREATE', 'professor_assignments', $lastAssignmentId, null, $newData);

                // Registrar actividad en el log de actividades
                $professorName = '';
                $subjectName = '';
                $professor = $this->userModel->find($professorId);
                $subject = $this->subjectModel->find($subjectId);
                if ($professor) $professorName = $professor['name'];
                if ($subject) $subjectName = $subject['name'];
                ActivityHelper::logAssignmentCreate($lastAssignmentId, $professorName, $subjectName);

                // Crear notificación
                NotificationHelper::notifyAssignmentCreate($lastAssignmentId, $professorName, $subjectName);

                header('Location: ' . BASE_PATH . '/academic/assignments');
                exit();
            } else {
                echo "Error al guardar la asignación.";
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
        $assignment = $this->assignmentModel->find($id);

        if (!$assignment) {
            header('Location: ' . BASE_PATH . '/academic/assignments');
            exit();
        }

        $canManageAll = PermissionHelper::can('assignments', 'manage_all', $roles);
        $canEdit = PermissionHelper::can('assignments', 'edit', $roles);
        $isOwner = (int)($_SESSION['user_id'] ?? 0) === (int)($assignment['professor_id'] ?? 0);
        if (!$canManageAll && !($canEdit && $isOwner)) {
            header('Location: ' . BASE_PATH . '/academic/assignments');
            exit();
        }

        $professor = $this->userModel->find($assignment['professor_id']);
        $subject = $this->subjectModel->find($assignment['subject_id']);
        $pao = $this->paoModel->find($assignment['pao_id']);

        $assignment['professor_name'] = $professor['name'] ?? 'Desconocido';
        $assignment['subject_name'] = $subject['name'] ?? 'Desconocido';
        $assignment['pao_name'] = $pao['title'] ?? 'Desconocido';

        $pageTitle = 'Editar Asignación: ' . htmlspecialchars($assignment['id']);
        // Ruta de la vista actualizada
        require_once __DIR__ . '/../views/academic/edit-assignments.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldAssignment = $this->assignmentModel->find($id);
            if (!$oldAssignment) {
                header('Location: ' . BASE_PATH . '/academic/assignments');
                exit();
            }

            $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id'] ?? 0);
            $canManageAll = PermissionHelper::can('assignments', 'manage_all', $roles);
            $canEdit = PermissionHelper::can('assignments', 'edit', $roles);
            $isOwner = (int)($_SESSION['user_id'] ?? 0) === (int)($oldAssignment['professor_id'] ?? 0);
            if (!$canManageAll && !($canEdit && $isOwner)) {
                header('Location: ' . BASE_PATH . '/academic/assignments');
                exit();
            }

            $hoursPerWeek = isset($_POST['hours_per_week']) ? (float)$_POST['hours_per_week'] : null;
            $status = $_POST['status'] ?? 'Asignado';

            $data = ['hours_per_week' => $hoursPerWeek, 'status' => $status];

            if ($this->assignmentModel->update($id, $data)) {
                $userIdLog = $_SESSION['user_id'] ?? null;
                $newData = ['hours_per_week' => $hoursPerWeek, 'status' => $status];
                $oldData = ['hours_per_week' => $oldAssignment['hours_per_week'], 'status' => $oldAssignment['status']];
                $this->auditLogModel->logAction($userIdLog, 'UPDATE', 'professor_assignments', $id, $oldData, $newData);

                header('Location: ' . BASE_PATH . '/academic/assignments');
                exit();
            } else {
                echo "Error al actualizar la asignación.";
            }
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $assignment = $this->assignmentModel->find((int)$id);
        if (!$assignment) {
            header('Location: ' . BASE_PATH . '/academic/assignments');
            exit();
        }

        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        $canManageAll = PermissionHelper::can('assignments', 'manage_all', $roles);
        $canDelete = PermissionHelper::can('assignments', 'delete', $roles);
        $isOwner = (int)($_SESSION['user_id'] ?? 0) === (int)($assignment['professor_id'] ?? 0);
        if (!$canManageAll && !($canDelete && $isOwner)) {
            header('Location: ' . BASE_PATH . '/academic/assignments');
            exit();
        }

        if ($assignment) {
            $query = "DELETE FROM professor_assignments WHERE id = ?";
            $stmt = $this->assignmentModel->getConnection()->prepare($query);
            $stmt->execute([(int)$id]);
            $this->auditLogModel->logAction($_SESSION['user_id'], 'DELETE', 'professor_assignments', (int)$id, $assignment, null);
        }

        header('Location: ' . BASE_PATH . '/academic/assignments');
        exit();
    }
}
