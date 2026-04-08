<?php
// app/models/ContinuityModel.php

require_once __DIR__ . '/BaseModel.php';

class ContinuityModel extends BaseModel
{
    protected $table = 'continuity';

    public function __construct()
    {
        parent::__construct();
    }

    public function getContinuitiesWithDetails($userId = null, $userRoles = [])
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
                    c.professor_decision,
                    c.docencia_decision,
                    c.docencia_decision_by,
                    c.final_status,
                    c.created_at,
                    c.professor_decision_at,
                    c.docencia_decision_at,
                    u.name AS professor_name,
                    p.title AS pao_name,
                    ua.name AS docencia_approved_by_name
                  FROM " . $this->table . " c
                  JOIN users u ON c.professor_id = u.id
                  JOIN pao p ON c.pao_id = p.id
                  LEFT JOIN users ua ON c.docencia_decision_by = ua.id";
        
        $params = [];
        
        // Si es solo profesor, filtrar solo sus continuidades
        if ($isOnlyProfessor && $userId !== null) {
            $query .= " WHERE c.professor_id = :userId";
            $params[':userId'] = $userId;
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($professorId, $paoId)
    {
        $query = "INSERT INTO " . $this->table . " (professor_id, pao_id, final_status) VALUES (?, ?, 'Pendiente')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $paoId);
        return $stmt->execute();
    }

    /**
     * Actualiza dinámicamente la columna de decisión correspondiente (Profesor o Docencia).
     * @param int $id ID de la continuidad.
     * @param string $fieldToUpdate 'professor_decision' o 'docencia_decision'.
     * @param int $decision El valor de la decisión (0 o 1).
     * @param int|null $userId ID del usuario que aprueba (solo necesario para docencia).
     * @return bool
     */
    public function updateDecisionDynamically($id, $fieldToUpdate, $decision, $userId = null)
    {
        if ($fieldToUpdate === 'professor_decision') {
            return $this->processProfessorDecisionUpdate($id, $decision, $userId);
        }

        if ($fieldToUpdate === 'docencia_decision') {
            return $this->processDocenciaDecisionUpdate($id, $decision, $userId);
        }

        return false; // Campo no válido
    }

    // Método interno para procesar la actualización del Profesor (Antiguo updateProfessorDecision)
    private function processProfessorDecisionUpdate($id, $decision, $userId)
    {
        $query = "UPDATE " . $this->table . " SET professor_decision = ?, professor_decision_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $decision, PDO::PARAM_INT); // Usar INT ya que es tinyint
        $stmt->bindParam(2, $id);
        return $stmt->execute();
    }

    // Método interno para procesar la actualización de Docencia (Antiguo updateDocenciaDecision)
    private function processDocenciaDecisionUpdate($id, $decision, $approvedBy)
    {
        // 1. Obtener la decisión del profesor para calcular el estado final
        $current = $this->find($id);

        // El Super Administrador puede sobrescribir, así que usamos el valor del modelo si no se proporciona,
        // pero para calcular el estado final, necesitamos la última decisión del profesor.
        // NOTA: Para Super Admin, $current['professor_decision'] podría ser null. 
        // Si el admin modifica docencia, asumimos que el estado final depende de la docencia_decision y de la professor_decision previa.
        $professorDecision = $current['professor_decision'];

        // Lógica de Retención: Solo Retenido si AMBOS dicen SÍ (1)
        $finalStatus = ($professorDecision == 1 && $decision == 1) ? 'Aprobado' : 'Rechazado';

        // Si la decisión de Docencia/TH es NO, y la del profesor fue SÍ, el estado final es 'No Ractificado'.
        // Si ambas son NO, el estado final también es 'No Ractificado'.
        // Si la del profesor es NULL, la lógica debería forzar a que no se pueda registrar, pero si el Admin lo hace:
        if ($professorDecision === null && !$approvedBy) {
            // Esto solo lo haría el Admin, pero es mejor que la lógica de la vista lo evite.
            // Aquí forzamos 'No Ractificado' si la decisión del profesor es nula, salvo que sea Super Admin.
            // Si el Admin aprueba la decisión de Docencia/TH sin que el profesor haya respondido, la lógica puede ser compleja.
            // Mantendremos la lógica simple: si la Docencia/TH decide, el estado final se actualiza.
        }

        $query = "UPDATE " . $this->table . " SET docencia_decision = ?, docencia_decision_by = ?, docencia_decision_at = CURRENT_TIMESTAMP, final_status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $decision, PDO::PARAM_INT); // Usar INT
        $stmt->bindParam(2, $approvedBy);
        $stmt->bindParam(3, $finalStatus);
        $stmt->bindParam(4, $id);

        return $stmt->execute();
    }

    // Se recomienda añadir un método findById para obtener todos los detalles de una sola continuidad
    public function find($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

