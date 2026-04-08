<?php
// app/models/EvaluationModel.php

declare(strict_types=1); // Habilita la comprobación estricta de tipos

require_once __DIR__ . '/BaseModel.php';

class EvaluationModel extends BaseModel
{
    protected $table = 'evaluations';

    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $data)
    {
        $query = "INSERT INTO " . $this->table . " (
                                        professor_id, pao_id, evaluator_id, autoevaluacion, coevaluacion_20, coevaluacion_30, coevaluacion, heteroevaluacion, score, comments, initial_file_path, status, final_status
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['professor_id'],
            $data['pao_id'],
            $data['evaluator_id'],
            $data['autoevaluacion'] ?? 0,
                        $data['coevaluacion_20'] ?? 0,
                        $data['coevaluacion_30'] ?? 0,
            $data['coevaluacion'] ?? 0,
            $data['heteroevaluacion'] ?? 0,
            $data['score'],
            $data['comments'],
            $data['initial_file_path'],
            $data['status'],
            $data['final_status']
        ]);
    }

    public function update($id, array $data)
    {
        $query = "UPDATE " . $this->table . " SET
                    professor_id = ?, pao_id = ?, evaluator_id = ?, autoevaluacion = ?, coevaluacion_20 = ?, coevaluacion_30 = ?, coevaluacion = ?, heteroevaluacion = ?, score = ?, comments = ?,
                    status = ?, final_status = ?";
        
        $params = [
            $data['professor_id'],
            $data['pao_id'],
            $data['evaluator_id'],
            $data['autoevaluacion'] ?? 0,
            $data['coevaluacion_20'] ?? 0,
            $data['coevaluacion_30'] ?? 0,
            $data['coevaluacion'] ?? 0,
            $data['heteroevaluacion'] ?? 0,
            $data['score'],
            $data['comments'],
            $data['status'],
            $data['final_status']
        ];
        
        // Agregar campos opcionales solo si están definidos
        if (isset($data['initial_file_path'])) {
            $query .= ", initial_file_path = ?";
            $params[] = $data['initial_file_path'];
        }
        
        if (isset($data['signed_file_path'])) {
            $query .= ", signed_file_path = ?";
            $params[] = $data['signed_file_path'];
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Obtiene las evaluaciones con detalles, filtradas por rol de usuario.
     * @param int $userId El ID del usuario logueado.
     * @param array $userRoles Array de roles del usuario logueado.
     * @return array
     */
    public function getEvaluationsWithDetails(int $userId, array $userRoles = []): array
    {
        // Extraer nombres de roles del array
        $roleNames = array_column($userRoles, 'role_name');
        
        // Verificar si es Coordinador académico, Director de docencia o Super Administrador
        $canViewAll = in_array('Coordinador académico', $roleNames) || 
                      in_array('Director de docencia', $roleNames) ||
                      in_array('Talento humano', $roleNames) || 
                      in_array('Super Administrador', $roleNames);
        
        $query = "SELECT
                    e.id,
                    u_prof.name AS professor_name,
                    u_prof.escuela AS professor_school,
                    p.title AS pao_name,
                    u_eval.name AS evaluator_name,
                    e.score,
                    e.comments,
                    e.status,
                    e.final_status,
                    e.initial_file_path,
                    e.signed_file_path,
                    e.evaluation_date,
                    e.professor_id as evaluation_professor_id
                  FROM " . $this->table . " e
                  JOIN users u_prof ON e.professor_id = u_prof.id
                  JOIN pao p ON e.pao_id = p.id
                  JOIN users u_eval ON e.evaluator_id = u_eval.id";

        $params = [];
        
        // Si NO puede ver todas, filtrar solo sus evaluaciones
        if (!$canViewAll) {
            $query .= " WHERE e.professor_id = :userId";
            $params[':userId'] = $userId;
        }
        
        $query .= " ORDER BY e.evaluation_date DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una evaluación por su ID.
     * Se mantiene aquí para la verificación de permisos en el controlador.
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId(): string
    {
        return $this->db->lastInsertId();
    }
}
