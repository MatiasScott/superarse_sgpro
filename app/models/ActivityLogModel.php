<?php
// app/models/ActivityLogModel.php

require_once __DIR__ . '/BaseModel.php';

class ActivityLogModel extends BaseModel
{
    protected $table = 'activity_logs';
    
    public function createActivity($userId, $action, $tableName, $recordId, $description = null)
    {
        try {
            $query = "INSERT INTO activity_logs (user_id, action, table_name, record_id, description, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $userId, PDO::PARAM_INT);
            $stmt->bindParam(2, $action, PDO::PARAM_STR);
            $stmt->bindParam(3, $tableName, PDO::PARAM_STR);
            $stmt->bindParam(4, $recordId, PDO::PARAM_STR);
            $stmt->bindParam(5, $description, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ActivityLogModel::createActivity fallback: " . $e->getMessage());
            return false;
        }
    }
    
    public function getRecentActivities($limit = 20)
    {
        try {
            $query = "SELECT 
                        al.*,
                        u.name as user_name,
                        u.email as user_email
                      FROM activity_logs al 
                      LEFT JOIN users u ON al.user_id = u.id 
                      ORDER BY al.created_at DESC 
                      LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ActivityLogModel::getRecentActivities fallback: " . $e->getMessage());
            return [];
        }
    }
    
    public function getActivitiesByUser($userId, $limit = 10)
    {
        try {
            $query = "SELECT * FROM activity_logs 
                      WHERE user_id = ? 
                      ORDER BY created_at DESC 
                      LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $userId, PDO::PARAM_INT);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ActivityLogModel::getActivitiesByUser fallback: " . $e->getMessage());
            return [];
        }
    }
    
    public function getActivitiesByTable($tableName, $limit = 10)
    {
        try {
            $query = "SELECT 
                        al.*,
                        u.name as user_name
                      FROM activity_logs al 
                      LEFT JOIN users u ON al.user_id = u.id 
                      WHERE al.table_name = ? 
                      ORDER BY al.created_at DESC 
                      LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $tableName, PDO::PARAM_STR);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ActivityLogModel::getActivitiesByTable fallback: " . $e->getMessage());
            return [];
        }
    }
    
    public function formatActivityDescription($activity)
    {
        $actionMap = [
            'CREATE' => 'creó',
            'UPDATE' => 'actualizó',
            'DELETE' => 'eliminó'
        ];
        
        $tableMap = [
            'users' => 'un usuario',
            'professors' => 'un profesor',
            'paos' => 'un PAO',
            'subjects' => 'una materia',
            'assignments' => 'una asignación',
            'contracts' => 'un contrato',
            'invoices' => 'una factura',
            'evaluations' => 'una evaluación',
            'portfolios' => 'un portafolio',
            'continuity' => 'un proceso de continuidad'
        ];
        
        $action = $actionMap[$activity['action']] ?? $activity['action'];
        $table = $tableMap[$activity['table_name']] ?? $activity['table_name'];
        
        if ($activity['description']) {
            return "{$action} {$table}: {$activity['description']}";
        } else {
            return "{$action} {$table}";
        }
    }
    
    public function getActivityIcon($tableName, $action)
    {
        $icons = [
            'users' => 'user',
            'professors' => 'user-tie',
            'paos' => 'calendar-alt',
            'subjects' => 'book',
            'assignments' => 'tasks',
            'contracts' => 'file-contract',
            'invoices' => 'file-invoice',
            'evaluations' => 'clipboard-check',
            'portfolios' => 'folder',
            'continuity' => 'sync'
        ];
        
        return $icons[$tableName] ?? 'edit';
    }
    
    public function getActivityColor($action)
    {
        $colors = [
            'CREATE' => 'green',
            'UPDATE' => 'blue',
            'DELETE' => 'red'
        ];
        
        return $colors[$action] ?? 'gray';
    }
}