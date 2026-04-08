<?php
// app/helpers/NotificationHelper.php

require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';

class NotificationHelper
{
    private static $notificationModel;
    private static $userModel;
    private static $roleModel;

    private static function getNotificationModel()
    {
        if (!self::$notificationModel) {
            self::$notificationModel = new NotificationModel();
        }
        return self::$notificationModel;
    }

    private static function getUserModel()
    {
        if (!self::$userModel) {
            self::$userModel = new UserModel();
        }
        return self::$userModel;
    }

    private static function getRoleModel()
    {
        if (!self::$roleModel) {
            self::$roleModel = new RoleModel();
        }
        return self::$roleModel;
    }

    /**
     * Crear notificación para todos los administradores
     */
    private static function notifyAdministrators($title, $message, $type, $tableName, $recordId, $action)
    {
        // Obtener todos los usuarios con roles administrativos
        $userModel = self::getUserModel();
        $roleModel = self::getRoleModel();
        
        // Roles que deben recibir notificaciones
        $adminRoles = ['Super Administrador', 'Coordinador académico', 'Director de docencia'];
        
        foreach ($adminRoles as $roleName) {
            $admins = $userModel->getUsersByRole($roleName);
            foreach ($admins as $admin) {
                self::getNotificationModel()->createNotification(
                    $admin['id'],
                    $title,
                    $message,
                    $type,
                    $tableName,
                    $recordId,
                    $action
                );
            }
        }
    }

    /**
     * Notificación para creación de usuario
     */
    public static function notifyUserCreate($userId, $userName)
    {
        $title = "Nuevo Usuario Creado";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'users', ['name' => $userName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'users', $userId, 'CREATE');
    }

    /**
     * Notificación para actualización de usuario
     */
    public static function notifyUserUpdate($userId, $userName)
    {
        $title = "Usuario Actualizado";
        $message = self::getNotificationModel()->formatNotificationMessage('UPDATE', 'users', ['name' => $userName]);
        $type = self::getNotificationModel()->getNotificationType('UPDATE');
        
        self::notifyAdministrators($title, $message, $type, 'users', $userId, 'UPDATE');
    }

    /**
     * Notificación para eliminación de usuario
     */
    public static function notifyUserDelete($userId, $userName)
    {
        $title = "Usuario Eliminado";
        $message = self::getNotificationModel()->formatNotificationMessage('DELETE', 'users', ['name' => $userName]);
        $type = self::getNotificationModel()->getNotificationType('DELETE');
        
        self::notifyAdministrators($title, $message, $type, 'users', $userId, 'DELETE');
    }

    /**
     * Notificación para creación de PAO
     */
    public static function notifyPaoCreate($paoId, $paoName)
    {
        $title = "Nuevo PAO Creado";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'pao', ['name' => $paoName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'pao', $paoId, 'CREATE');
    }

    /**
     * Notificación para actualización de PAO
     */
    public static function notifyPaoUpdate($paoId, $paoName)
    {
        $title = "PAO Actualizado";
        $message = self::getNotificationModel()->formatNotificationMessage('UPDATE', 'pao', ['name' => $paoName]);
        $type = self::getNotificationModel()->getNotificationType('UPDATE');

        self::notifyAdministrators($title, $message, $type, 'pao', $paoId, 'UPDATE');
    }

    /**
     * Notificación para eliminación de PAO
     */
    public static function notifyPaoDelete($paoId, $paoName)
    {
        $title = "PAO Eliminado";
        $message = self::getNotificationModel()->formatNotificationMessage('DELETE', 'pao', ['name' => $paoName]);
        $type = self::getNotificationModel()->getNotificationType('DELETE');

        self::notifyAdministrators($title, $message, $type, 'pao', $paoId, 'DELETE');
    }

    /**
     * Notificación para creación de asignatura
     */
    public static function notifySubjectCreate($subjectId, $subjectName)
    {
        $title = "Nueva Asignatura Creada";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'subjects', ['name' => $subjectName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'subjects', $subjectId, 'CREATE');
    }

    /**
     * Notificación para creación de asignación
     */
    public static function notifyAssignmentCreate($assignmentId, $professorName, $subjectName)
    {
        $title = "Nueva Asignación Creada";
        $description = "$professorName - $subjectName";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'professor_assignments', ['description' => $description]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'professor_assignments', $assignmentId, 'CREATE');
    }

    /**
     * Notificación para creación de contrato
     */
    public static function notifyContractCreate($contractId, $professorName)
    {
        $title = "Nuevo Contrato Creado";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'contracts', ['professor_name' => $professorName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'contracts', $contractId, 'CREATE');
    }

    /**
     * Notificación para creación de factura
     */
    public static function notifyInvoiceCreate($invoiceId, $professorName, $amount, $professorId = null)
    {
        $title = "Nueva Factura Creada";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'invoices', [
            'professor_name' => $professorName,
            'amount' => $amount
        ]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        // Notificar a los administradores
        self::notifyAdministrators($title, $message, $type, 'invoices', $invoiceId, 'CREATE');
        
        // Notificar al profesor específico si se proporciona su ID
        if ($professorId) {
            $professorTitle = "Tu Factura ha sido Creada";
            $professorMessage = "Se ha creado una nueva factura a tu nombre por un monto de $" . number_format($amount, 2) . ". El equipo administrativo la revisará pronto.";
            $professorType = 'info';
            
            self::getNotificationModel()->createNotification(
                $professorId,
                $professorTitle,
                $professorMessage,
                $professorType,
                'invoices',
                $invoiceId,
                'CREATE'
            );
        }
    }

    /**
     * Notificación para creación de evaluación
     */
    public static function notifyEvaluationCreate($evaluationId, $professorName)
    {
        $title = "Nueva Evaluación Creada";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'evaluations', ['professor_name' => $professorName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'evaluations', $evaluationId, 'CREATE');
    }

    /**
     * Notificación para creación de portafolio
     */
    public static function notifyPortfolioCreate($portfolioId, $professorName)
    {
        $title = "Nuevo Portafolio Creado";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'portfolios', ['professor_name' => $professorName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'portfolios', $portfolioId, 'CREATE');
    }

    /**
     * Notificación para creación de continuidad
     */
    public static function notifyContinuityCreate($continuityId, $professorName)
    {
        $title = "Nuevo Registro de Continuidad";
        $message = self::getNotificationModel()->formatNotificationMessage('CREATE', 'continuity', ['professor_name' => $professorName]);
        $type = self::getNotificationModel()->getNotificationType('CREATE');
        
        self::notifyAdministrators($title, $message, $type, 'continuity', $continuityId, 'CREATE');
    }

    /**
     * Obtener el número de notificaciones no leídas para el usuario actual
     */
    public static function getUnreadCount($userId)
    {
        return self::getNotificationModel()->getUnreadCount($userId);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public static function markAllAsRead($userId)
    {
        return self::getNotificationModel()->markAllAsRead($userId);
    }

    /**
     * Obtener notificaciones del usuario
     */
    public static function getUserNotifications($userId, $limit = 20, $offset = 0, $onlyUnread = false)
    {
        return self::getNotificationModel()->getUserNotifications($userId, $limit, $offset, $onlyUnread);
    }

    /**
     * Marcar notificación específica como leída
     */
    public static function markAsRead($notificationId, $userId = null)
    {
        return self::getNotificationModel()->markAsRead($notificationId, $userId);
    }

    /**
     * Notificación para actualización de factura (cambio de estado)
     */
    public static function notifyInvoiceStatusChange($invoiceId, $professorId, $professorName, $newStatus, $amount)
    {
        // Notificar a administradores
        $title = "Estado de Factura Actualizado";
        $message = "La factura de {$professorName} por $" . number_format($amount, 2) . " ha cambiado a estado: {$newStatus}";
        $type = self::getNotificationModel()->getNotificationType('UPDATE');
        
        self::notifyAdministrators($title, $message, $type, 'invoices', $invoiceId, 'UPDATE');
        
        // Notificar al profesor
        $professorTitle = "Estado de tu Factura Actualizado";
        $statusMessages = [
            'pagado' => "¡Buenas noticias! Tu factura por $" . number_format($amount, 2) . " ha sido pagada.",
            'pendiente' => "Tu factura por $" . number_format($amount, 2) . " está pendiente de revisión.",
            'rechazado' => "Tu factura por $" . number_format($amount, 2) . " requiere correcciones. Por favor, contacta con administración."
        ];
        $professorMessage = $statusMessages[strtolower($newStatus)] ?? "El estado de tu factura ha cambiado a: {$newStatus}";
        
        self::getNotificationModel()->createNotification(
            $professorId,
            $professorTitle,
            $professorMessage,
            strtolower($newStatus) === 'pagado' ? 'success' : 'info',
            'invoices',
            $invoiceId,
            'UPDATE'
        );
    }

    /**
     * Notificación para asignación de asignatura a profesor
     */
    public static function notifySubjectAssignment($assignmentId, $professorId, $professorName, $subjectName)
    {
        // Notificar a administradores
        $title = "Nueva Asignación de Asignatura";
        $message = "Se ha asignado la asignatura '{$subjectName}' al profesor {$professorName}";
        $type = 'info';
        
        self::notifyAdministrators($title, $message, $type, 'assignments', $assignmentId, 'CREATE');
        
        // Notificar al profesor
        $professorTitle = "Nueva Asignatura Asignada";
        $professorMessage = "Se te ha asignado la asignatura: {$subjectName}. Revisa los detalles en tu sección de Asignaciones.";
        
        self::getNotificationModel()->createNotification(
            $professorId,
            $professorTitle,
            $professorMessage,
            'info',
            'assignments',
            $assignmentId,
            'CREATE'
        );
    }
}
?>