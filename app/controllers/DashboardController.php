<?php
// app/controllers/DashboardController.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/ActivityLogModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class DashboardController
{
    private $userModel;
    private $roleModel;
    private $activityLogModel;
    private $notificationModel;

    public function __construct()
    {
        // Instancia de los modelos necesarios
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        // Verifica si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        // Obtener información del usuario
        $user = $this->userModel->find($_SESSION['user_id']);
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        // Obtener actividades recientes
        $recentActivities = $this->activityLogModel->getRecentActivities(15);
        
        // Obtener notificaciones del usuario
        $notifications = $this->notificationModel->getUserNotifications($_SESSION['user_id'], 10);
        $unreadCount = $this->notificationModel->getUnreadCount($_SESSION['user_id']);

        $pageTitle = 'Dashboard - SGPRO';
        require_once __DIR__ . '/../views/dashboard/index.php';
    }

    public function markNotificationAsRead($notificationId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $this->notificationModel->markAsRead($notificationId, $_SESSION['user_id']);
        header('Location: ' . BASE_PATH . '/dashboard');
        exit();
    }

    public function markAllNotificationsAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $this->notificationModel->markAllAsRead($_SESSION['user_id']);
        header('Location: ' . BASE_PATH . '/dashboard');
        exit();
    }
}