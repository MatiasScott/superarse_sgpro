<?php
// app/models/AuditLogModel.php

require_once __DIR__ . '/BaseModel.php';

class AuditLogModel extends BaseModel {
    protected $table = "audit_logs";

    public function __construct() {
        parent::__construct();
    }

    public function logAction($userId, $action, $tableName, $recordId, $oldData = null, $newData = null) {
        $query = "INSERT INTO " . $this->table . " (user_id, action, table_name, record_id, old_data, new_data) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);

        // Convertir los datos a formato JSON para almacenarlos
        $oldJson = $oldData ? json_encode($oldData) : null;
        $newJson = $newData ? json_encode($newData) : null;

        $stmt->bindParam(1, $userId);
        $stmt->bindParam(2, $action);
        $stmt->bindParam(3, $tableName);
        $stmt->bindParam(4, $recordId);
        $stmt->bindParam(5, $oldJson);
        $stmt->bindParam(6, $newJson);

        return $stmt->execute();
    }
}