<?php
// app/models/AssignmentModel.php

require_once __DIR__ . '/BaseModel.php';

class AssignmentModel extends BaseModel
{
    protected $table = 'professor_assignments';

    public function __construct()
    {
        parent::__construct();
    }

    // Método para obtener asignaciones con detalles según el rol del usuario
    public function getAssignmentsWithDetails($userId = null, $userRoles = [])
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
                    pa.id,
                    pa.professor_id,
                    pa.hours_per_week,
                    pa.status,
                    pa.created_at,
                    u.name AS professor_name,
                    s.name AS subject_name,
                    p.title AS pao_name
                  FROM " . $this->table . " pa
                  JOIN users u ON pa.professor_id = u.id
                  JOIN subjects s ON pa.subject_id = s.id
                  JOIN pao p ON pa.pao_id = p.id";
        
        $params = [];
        
        // Si es solo profesor, filtrar solo sus asignaciones
        if ($isOnlyProfessor && $userId !== null) {
            $query .= " WHERE pa.professor_id = :userId";
            $params[':userId'] = $userId;
        }
        
        $query .= " ORDER BY pa.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para crear una nueva asignación
    public function create($professorId, $subjectId, $paoId, $hoursPerWeek, $status)
    {
        $query = "INSERT INTO " . $this->table . " (professor_id, subject_id, pao_id, hours_per_week, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $subjectId);
        $stmt->bindParam(3, $paoId);
        $stmt->bindParam(4, $hoursPerWeek);
        $stmt->bindParam(5, $status);
        return $stmt->execute();
    }

    // Método para actualizar una asignación existente
    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table . " SET hours_per_week = ?, status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $data['hours_per_week']);
        $stmt->bindParam(2, $data['status']);
        $stmt->bindParam(3, $id);
        return $stmt->execute();
    }
}
