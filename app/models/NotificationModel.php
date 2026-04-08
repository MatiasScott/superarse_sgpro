<?php
// app/models/NotificationModel.php

require_once __DIR__ . '/BaseModel.php';

class NotificationModel extends BaseModel
{
    protected $table = 'notifications';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Crear una nueva notificación
     */
    public function createNotification($userId, $title, $message, $type, $tableName, $recordId, $action)
    {
        try {
            $sql = "INSERT INTO notifications (user_id, title, message, type, table_name, record_id, action) 
                    VALUES (:user_id, :title, :message, :type, :table_name, :record_id, :action)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':message' => $message,
                ':type' => $type,
                ':table_name' => $tableName,
                ':record_id' => $recordId,
                ':action' => $action
            ]);
        } catch (PDOException $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener notificaciones del usuario con paginación
     */
    public function getUserNotifications($userId, $limit = 20, $offset = 0, $onlyUnread = false)
    {
        try {
            $whereClause = "WHERE user_id = :user_id";
            if ($onlyUnread) {
                $whereClause .= " AND is_read = FALSE";
            }

            $sql = "SELECT 
                        n.*,
                        u.name as user_name
                    FROM notifications n 
                    LEFT JOIN users u ON n.user_id = u.id 
                    $whereClause 
                    ORDER BY n.created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todas las notificaciones para administradores
     */
    public function getAllNotifications($limit = 50, $offset = 0, $onlyUnread = false)
    {
        try {
            $whereClause = "";
            if ($onlyUnread) {
                $whereClause = "WHERE n.is_read = FALSE";
            }

            $sql = "SELECT 
                        n.*,
                        u.name as user_name,
                        target_user.name as target_user_name
                    FROM notifications n 
                    LEFT JOIN users u ON n.user_id = u.id
                    LEFT JOIN users target_user ON n.record_id = target_user.id AND n.table_name = 'users'
                    $whereClause
                    ORDER BY n.created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar todas las notificaciones no leídas del sistema (para administradores)
     */
    public function getAllUnreadCount()
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM notifications 
                    WHERE is_read = FALSE";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Error counting all unread notifications: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead($notificationId, $userId = null)
    {
        try {
            $whereClause = "WHERE id = :id";
            $params = [':id' => $notificationId];
            
            if ($userId) {
                $whereClause .= " AND user_id = :user_id";
                $params[':user_id'] = $userId;
            }

            $sql = "UPDATE notifications 
                    SET is_read = TRUE, read_at = CURRENT_TIMESTAMP 
                    $whereClause";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar todas las notificaciones del usuario como leídas
     */
    public function markAllAsRead($userId)
    {
        try {
            $sql = "UPDATE notifications 
                    SET is_read = TRUE, read_at = CURRENT_TIMESTAMP 
                    WHERE user_id = :user_id AND is_read = FALSE";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar notificaciones no leídas del usuario
     */
    public function getUnreadCount($userId)
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM notifications 
                    WHERE user_id = :user_id AND is_read = FALSE";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Error counting unread notifications: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Eliminar notificaciones antiguas (opcional, para mantener limpia la tabla)
     */
    public function deleteOldNotifications($daysOld = 30)
    {
        try {
            $sql = "DELETE FROM notifications 
                    WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':days' => $daysOld]);
        } catch (PDOException $e) {
            error_log("Error deleting old notifications: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener notificación por ID
     */
    public function getNotificationById($id, $userId = null)
    {
        try {
            $whereClause = "WHERE n.id = :id";
            $params = [':id' => $id];
            
            if ($userId) {
                $whereClause .= " AND n.user_id = :user_id";
                $params[':user_id'] = $userId;
            }

            $sql = "SELECT 
                        n.*,
                        u.name as user_name
                    FROM notifications n 
                    LEFT JOIN users u ON n.user_id = u.id 
                    $whereClause";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching notification by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Formatear mensaje de notificación según el tipo de acción
     */
    public function formatNotificationMessage($action, $tableName, $recordData = [])
    {
        $messages = [
            'users' => [
                'CREATE' => 'Se ha creado un nuevo usuario: ' . ($recordData['name'] ?? 'Usuario'),
                'UPDATE' => 'Se ha actualizado el usuario: ' . ($recordData['name'] ?? 'Usuario'),
                'DELETE' => 'Se ha eliminado el usuario: ' . ($recordData['name'] ?? 'Usuario')
            ],
            'pao' => [
                'CREATE' => 'Se ha creado un nuevo PAO: ' . ($recordData['name'] ?? 'PAO'),
                'UPDATE' => 'Se ha actualizado el PAO: ' . ($recordData['name'] ?? 'PAO'),
                'DELETE' => 'Se ha eliminado el PAO: ' . ($recordData['name'] ?? 'PAO')
            ],
            'subjects' => [
                'CREATE' => 'Se ha creado una nueva asignatura: ' . ($recordData['name'] ?? 'Asignatura'),
                'UPDATE' => 'Se ha actualizado la asignatura: ' . ($recordData['name'] ?? 'Asignatura'),
                'DELETE' => 'Se ha eliminado la asignatura: ' . ($recordData['name'] ?? 'Asignatura')
            ],
            'professor_assignments' => [
                'CREATE' => 'Se ha creado una nueva asignación: ' . ($recordData['description'] ?? 'Asignación'),
                'UPDATE' => 'Se ha actualizado la asignación: ' . ($recordData['description'] ?? 'Asignación'),
                'DELETE' => 'Se ha eliminado la asignación: ' . ($recordData['description'] ?? 'Asignación')
            ],
            'contracts' => [
                'CREATE' => 'Se ha creado un nuevo contrato para: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'UPDATE' => 'Se ha actualizado el contrato de: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'DELETE' => 'Se ha eliminado el contrato de: ' . ($recordData['professor_name'] ?? 'Profesor')
            ],
            'invoices' => [
                'CREATE' => 'Se ha creado una nueva factura por $' . ($recordData['amount'] ?? '0') . ' para: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'UPDATE' => 'Se ha actualizado la factura de: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'DELETE' => 'Se ha eliminado la factura de: ' . ($recordData['professor_name'] ?? 'Profesor')
            ],
            'evaluations' => [
                'CREATE' => 'Se ha creado una nueva evaluación para: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'UPDATE' => 'Se ha actualizado la evaluación de: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'DELETE' => 'Se ha eliminado la evaluación de: ' . ($recordData['professor_name'] ?? 'Profesor')
            ],
            'portfolios' => [
                'CREATE' => 'Se ha creado un nuevo portafolio para: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'UPDATE' => 'Se ha actualizado el portafolio de: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'DELETE' => 'Se ha eliminado el portafolio de: ' . ($recordData['professor_name'] ?? 'Profesor')
            ],
            'continuity' => [
                'CREATE' => 'Se ha registrado continuidad para: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'UPDATE' => 'Se ha actualizado la continuidad de: ' . ($recordData['professor_name'] ?? 'Profesor'),
                'DELETE' => 'Se ha eliminado el registro de continuidad de: ' . ($recordData['professor_name'] ?? 'Profesor')
            ]
        ];

        return $messages[$tableName][$action] ?? "Se ha realizado una acción $action en $tableName";
    }

    /**
     * Obtener tipo de notificación según la acción
     */
    public function getNotificationType($action)
    {
        $types = [
            'CREATE' => 'success',
            'UPDATE' => 'info',
            'DELETE' => 'warning'
        ];

        return $types[$action] ?? 'info';
    }
}
?>