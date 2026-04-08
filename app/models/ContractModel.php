<?php
// app/models/ContractModel.php

require_once __DIR__ . '/BaseModel.php';

class ContractModel extends BaseModel
{
    protected $table = 'contracts';

    public function __construct()
    {
        parent::__construct();
    }

    public function create($professorId, $paoId, $startDate, $endDate, $status, $documentPath)
    {
        $query = "INSERT INTO " . $this->table . " (professor_id, pao_id, start_date, end_date, status, document_path) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $paoId);
        $stmt->bindParam(3, $startDate);
        $stmt->bindParam(4, $endDate);
        $stmt->bindParam(5, $status);
        $stmt->bindParam(6, $documentPath);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table . " SET start_date = ?, end_date = ?, status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $data['start_date']);
        $stmt->bindParam(2, $data['end_date']);
        $stmt->bindParam(3, $data['status']);
        $stmt->bindParam(4, $id);
        return $stmt->execute();
    }

    public function getContractsWithDetails($userId = null, $userRoles = [])
    {
        // Extraer nombres de roles del array
        $roleNames = array_column($userRoles, 'role_name');
        
        // Verificar si es solo Profesor (sin otros roles administrativos)
        $isOnlyProfessor = in_array('Profesor', $roleNames) && 
                          !in_array('Coordinador académico', $roleNames) && 
                          !in_array('Director de docencia', $roleNames) && 
                          !in_array('Super Administrador', $roleNames) &&
                          !in_array('Talento humano', $roleNames);
        
        $query = "SELECT 
                    c.id,
                    c.professor_id,
                    c.start_date,
                    c.end_date,
                    c.status,
                    c.document_path,
                    c.created_at,
                    u.name AS professor_name,
                    pao.title AS pao_name
                  FROM " . $this->table . " c
                  JOIN users u ON c.professor_id = u.id
                  JOIN pao ON c.pao_id = pao.id";
        
        $params = [];
        
        // Si es solo profesor, filtrar solo sus contratos
        if ($isOnlyProfessor && $userId !== null) {
            $query .= " WHERE c.professor_id = :userId";
            $params[':userId'] = $userId;
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateContract($id, $startDate, $endDate, $status, $newDocumentPath = null)
    {
        // Si se proporciona una nueva ruta de documento, actualiza ese campo
        if ($newDocumentPath !== null) {
            $sql = "UPDATE contracts SET start_date = :start_date, end_date = :end_date, status = :status, document_path = :document_path WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':document_path', $newDocumentPath);
        } else {
            // Si no, actualiza solo los otros campos
            $sql = "UPDATE contracts SET start_date = :start_date, end_date = :end_date, status = :status WHERE id = :id";
            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    /**
     * Obtiene el contrato activo de un profesor
     */
    public function getActiveContractByProfessor($professorId)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE professor_id = ? AND status = 'Activo'
                  ORDER BY created_at DESC
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$professorId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
