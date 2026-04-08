<?php
// app/models/PortfolioModel.php

require_once __DIR__ . '/BaseModel.php';



class PortfolioModel extends BaseModel
{
    protected $table = 'portfolios';

    public function __construct()
    {
        parent::__construct();
    }

    // Este método actualizado para incluir portfolio_type
    public function create(array $data)
    {
        // Construir la consulta dinámicamente basada en las columnas presentes en $data
        $columns = ['professor_id', 'pao_id', 'portfolio_type', 'unit_number'];
        $values = [$data['professor_id'], $data['pao_id'], $data['portfolio_type'] ?? 'academico', $data['unit_number']];
        
        // Agregar columnas dinámicas si existen en $data
        $possibleColumns = [
            'docencia_path', 'practicas_path', 'titulacion_path',
            'docencia_academico_path', 'practicas_academico_path', 'titulacion_academico_path',
            'docencia_practico_path', 'practicas_practico_path', 'titulacion_practico_path',
            'docencia_titulacion_path', 'practicas_titulacion_path', 'titulacion_titulacion_path',
            'unit_approved', 'approved_at'
        ];
        
        foreach ($possibleColumns as $col) {
            if (isset($data[$col])) {
                $columns[] = $col;
                $values[] = $data[$col];
            }
        }
        
        $placeholders = array_fill(0, count($values), '?');
        $query = "INSERT INTO " . $this->table . " (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($query);
        
        // Bind todos los valores
        foreach ($values as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }
        
        return $stmt->execute();
    }
    
    // Este método actualizado para incluir aprobación y columnas dinámicas por tipo
    public function update($id, array $data)
    {
        $fields = [];
        $params = [];
        
        // Columnas antiguas (por compatibilidad)
        if (isset($data['docencia_path'])) {
            $fields[] = "docencia_path = ?";
            $params[] = $data['docencia_path'];
        }
        
        if (isset($data['practicas_path'])) {
            $fields[] = "practicas_path = ?";
            $params[] = $data['practicas_path'];
        }
        
        if (isset($data['titulacion_path'])) {
            $fields[] = "titulacion_path = ?";
            $params[] = $data['titulacion_path'];
        }

        // Nuevas columnas separadas por tipo
        if (isset($data['docencia_academico_path'])) {
            $fields[] = "docencia_academico_path = ?";
            $params[] = $data['docencia_academico_path'];
        }
        
        if (isset($data['docencia_practico_path'])) {
            $fields[] = "docencia_practico_path = ?";
            $params[] = $data['docencia_practico_path'];
        }
        
        if (isset($data['practicas_practico_path'])) {
            $fields[] = "practicas_practico_path = ?";
            $params[] = $data['practicas_practico_path'];
        }
        
        if (isset($data['docencia_titulacion_path'])) {
            $fields[] = "docencia_titulacion_path = ?";
            $params[] = $data['docencia_titulacion_path'];
        }
        
        if (isset($data['practicas_titulacion_path'])) {
            $fields[] = "practicas_titulacion_path = ?";
            $params[] = $data['practicas_titulacion_path'];
        }
        
        if (isset($data['titulacion_titulacion_path'])) {
            $fields[] = "titulacion_titulacion_path = ?";
            $params[] = $data['titulacion_titulacion_path'];
        }
        
        if (isset($data['unit_approved'])) {
            $fields[] = "unit_approved = ?";
            $params[] = $data['unit_approved'];
        }
        
        if (isset($data['approved_by'])) {
            $fields[] = "approved_by = ?";
            $params[] = $data['approved_by'];
        }
        
        if (isset($data['approved_at'])) {
            $fields[] = "approved_at = ?";
            $params[] = $data['approved_at'];
        }
        
        if (isset($data['portfolio_type'])) {
            $fields[] = "portfolio_type = ?";
            $params[] = $data['portfolio_type'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);

        return $stmt->execute($params);
    }

    public function updatePortfolioType($professorId, $paoId, $portfolioType)
    {
        $query = "UPDATE " . $this->table . " SET portfolio_type = ? WHERE professor_id = ? AND pao_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$portfolioType, $professorId, $paoId]);
    }

    // Método para obtener portafolios según el rol del usuario
    public function getPortfoliosWithDetails($userid, $userRoles = [])
    {
        // Extraer nombres de roles del array
        $roleNames = array_column($userRoles, 'role_name');
        
        // Verificar si es Coordinador académico, Director de docencia o Super Administrador
        $canViewAll = in_array('Coordinador académico', $roleNames) || 
                      in_array('Director de docencia', $roleNames) ||
                      in_array('Talento humano', $roleNames) || 
                      in_array('Super Administrador', $roleNames);
        
        if ($canViewAll) {
            // Coordinador, Director o Super Admin ven todos los portafolios
            $query = "
                SELECT 
                    p.id, 
                    p.professor_id, 
                    u.name AS professor_name, 
                    p.pao_id, 
                    pa.title AS pao_name,
                    p.unit_number,
                    p.portfolio_type,
                    p.unit_approved,
                    p.docencia_path,
                    p.practicas_path,
                    p.titulacion_path,
                    p.docencia_academico_path,
                    p.docencia_practico_path,
                    p.practicas_practico_path,
                    p.docencia_titulacion_path,
                    p.practicas_titulacion_path,
                    p.titulacion_titulacion_path
                FROM " . $this->table . " AS p
                JOIN users AS u ON p.professor_id = u.id
                JOIN pao AS pa ON p.pao_id = pa.id
                ORDER BY u.name, pa.title, p.unit_number ASC
            ";
        } else {
            // Los profesores solo ven sus propios portafolios
            $query = "
                SELECT 
                    p.id, 
                    p.professor_id, 
                    u.name AS professor_name, 
                    p.pao_id, 
                    pa.title AS pao_name,
                    p.unit_number,
                    p.portfolio_type,
                    p.unit_approved,
                    p.docencia_path,
                    p.practicas_path,
                    p.titulacion_path,
                    p.docencia_academico_path,
                    p.docencia_practico_path,
                    p.practicas_practico_path,
                    p.docencia_titulacion_path,
                    p.practicas_titulacion_path,
                    p.titulacion_titulacion_path
                FROM " . $this->table . " AS p
                JOIN users AS u ON p.professor_id = u.id
                JOIN pao AS pa ON p.pao_id = pa.id
                WHERE p.professor_id = :userid
                ORDER BY u.name, pa.title, p.unit_number ASC
            ";
        }
        
        $stmt = $this->db->prepare($query);
        
        // Si es profesor (no puede ver todos), bindear el parámetro userid
        if (!$canViewAll) {
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar los resultados por profesor y PAO
        $groupedPortfolios = [];
        foreach ($results as $row) {
            $key = $row['professor_id'] . '-' . $row['pao_id'];
            if (!isset($groupedPortfolios[$key])) {
                $groupedPortfolios[$key] = [
                    'id' => $row['id'],
                    'professor_id' => $row['professor_id'],
                    'professor_name' => $row['professor_name'],
                    'pao_id' => $row['pao_id'],
                    'pao_name' => $row['pao_name'],
                    'units' => []
                ];
            }
            $groupedPortfolios[$key]['units'][] = [
                'unit_number' => $row['unit_number'],
                'portfolio_type' => $row['portfolio_type'],
                'unit_approved' => $row['unit_approved'],
                'docencia_path' => $row['docencia_path'],
                'practicas_path' => $row['practicas_path'],
                'titulacion_path' => $row['titulacion_path'],
                'docencia_academico_path' => $row['docencia_academico_path'],
                'docencia_practico_path' => $row['docencia_practico_path'],
                'practicas_practico_path' => $row['practicas_practico_path'],
                'docencia_titulacion_path' => $row['docencia_titulacion_path'],
                'practicas_titulacion_path' => $row['practicas_titulacion_path'],
                'titulacion_titulacion_path' => $row['titulacion_titulacion_path']
            ];
        }

        return array_values($groupedPortfolios);
    }

    // Y el resto de los métodos que ya tenías...
    public function findByKeys($professorId, $paoId, $unitNumber)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE professor_id = ? AND pao_id = ? AND unit_number = ?";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $paoId);
        $stmt->bindParam(3, $unitNumber);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getPortfolio($professorId, $paoId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE professor_id = ? AND pao_id = ? ORDER BY unit_number ASC";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $paoId);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getLastInsertedId()
    {
        return $this->db->lastInsertId();
    }

    // Obtiene todos los portafolios de un profesor (sin filtrar por PAO)
    public function getPortfoliosByProfessor($professorId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE professor_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}