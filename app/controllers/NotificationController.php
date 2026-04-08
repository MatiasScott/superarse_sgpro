<?php
// app/controllers/NotificationController.php

require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';
require_once __DIR__ . '/../helpers/PermissionHelper.php';

class NotificationController
{
    private $notificationModel;
    private $userModel;
    private $roleModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Mostrar todas las notificaciones del usuario o de todos (según rol)
     */
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $roles = $this->roleModel->getRolesByUserId($userId);
        PermissionHelper::enforce('notifications', 'view', $roles, '/dashboard');

        // Si tiene manage_all en notificaciones, puede ver el stream global
        $canViewAll = PermissionHelper::can('notifications', 'manage_all', $roles);
        
        // Obtener parámetros de paginación
        $page = $_GET['page'] ?? 1;
        $limit = 50; // Aumentado a 50 para roles administrativos
        $offset = ($page - 1) * $limit;
        
        // Obtener filtro
        $onlyUnread = isset($_GET['unread']) && $_GET['unread'] == '1';

        // Obtener notificaciones según el rol
        if ($canViewAll) {
            // Administradores ven TODAS las notificaciones del sistema
            $notifications = $this->notificationModel->getAllNotifications($limit, $offset, $onlyUnread);
            $unreadCount = $this->notificationModel->getAllUnreadCount();
        } else {
            // Usuarios normales solo ven sus propias notificaciones
            $notifications = $this->notificationModel->getUserNotifications($userId, $limit, $offset, $onlyUnread);
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
        }

        $pageTitle = 'Notificaciones';
        require_once __DIR__ . '/../views/notifications/index.php';
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead($notificationId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAsRead($notificationId, $userId);

        // Redirigir de vuelta a las notificaciones
        header('Location: ' . BASE_PATH . '/notifications');
        exit();
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);

        // Redirigir de vuelta a las notificaciones
        header('Location: ' . BASE_PATH . '/notifications');
        exit();
    }

    /**
     * Obtener el contador de notificaciones no leídas (AJAX)
     */
    public function getUnreadCount()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $count = $this->notificationModel->getUnreadCount($userId);

        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit();
    }

    /**
     * Obtener notificaciones recientes (AJAX)
     */
    public function getRecent()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['notifications' => []]);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $limit = $_GET['limit'] ?? 10;
        
        $notifications = $this->notificationModel->getUserNotifications($userId, $limit);

        header('Content-Type: application/json');
        echo json_encode(['notifications' => $notifications]);
        exit();
    }

    /**
     * Ver detalle de una notificación específica
     */
    public function show($notificationId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $roles = $this->roleModel->getRolesByUserId($userId);
        
        $notification = $this->notificationModel->getNotificationById($notificationId, $userId);

        if (!$notification) {
            header('Location: ' . BASE_PATH . '/notifications');
            exit();
        }

        // Marcar como leída si no lo está
        if (!$notification['is_read']) {
            $this->notificationModel->markAsRead($notificationId, $userId);
            $notification['is_read'] = true;
        }

        $pageTitle = 'Detalle de Notificación';
        require_once __DIR__ . '/../views/notifications/show.php';
    }
}
?>