<?php
// app/models/InvoiceModel.php

require_once __DIR__ . '/BaseModel.php';

class InvoiceModel extends BaseModel {
    protected $table = "invoices";

    public function __construct() {
        parent::__construct();
    }
    
    public function create($professorId, $paoId, $unitNumber, $periodMonth, $periodYear, $invoiceDate, $amount, $status, $paymentProofPath = null, $comprobantePath = null, $observacion = null) {
        $query = "INSERT INTO " . $this->table . " (professor_id, pao_id, unit_number, period_month, period_year, invoice_date, amount, status, payment_proof_path, comprobante_path, observacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $paoId);
        $stmt->bindParam(3, $unitNumber);
        $stmt->bindParam(4, $periodMonth);
        $stmt->bindParam(5, $periodYear);
        $stmt->bindParam(6, $invoiceDate);
        $stmt->bindParam(7, $amount);
        $stmt->bindParam(8, $status);
        $stmt->bindParam(9, $paymentProofPath);
        $stmt->bindParam(10, $comprobantePath);
        $stmt->bindParam(11, $observacion);
        return $stmt->execute();
    }
    
    public function update($id, $unitNumber, $periodMonth, $periodYear, $amount, $status, $paymentProofPath = null, $comprobantePath = null, $observacion = null) {
        $query = "UPDATE " . $this->table . " SET unit_number = ?, period_month = ?, period_year = ?, amount = ?, status = ?, payment_proof_path = ?, comprobante_path = ?, observacion = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $unitNumber);
        $stmt->bindParam(2, $periodMonth);
        $stmt->bindParam(3, $periodYear);
        $stmt->bindParam(4, $amount);
        $stmt->bindParam(5, $status);
        $stmt->bindParam(6, $paymentProofPath);
        $stmt->bindParam(7, $comprobantePath);
        $stmt->bindParam(8, $observacion);
        $stmt->bindParam(9, $id);
        return $stmt->execute();
    }

    public function getInvoicesWithDetails($userId = null, $userRoles = [], $year = null) {
        // Extraer nombres de roles del array
        $roleNames = array_column($userRoles, 'role_name');
        
        // Verificar si es solo Profesor (sin otros roles administrativos)
        $isOnlyProfessor = in_array('Profesor', $roleNames) && 
                          !in_array('Coordinador académico', $roleNames) && 
                          !in_array('Director de docencia', $roleNames) && 
                          !in_array('Super Administrador', $roleNames) &&
                          !in_array('Talento humano', $roleNames);
        
        $query = "SELECT 
                    i.id,
                    i.professor_id,
                    i.pao_id,
                    i.unit_number,
                    i.period_month,
                    i.period_year,
                    i.invoice_date,
                    i.amount,
                    i.status,
                    i.payment_proof_path,
                    i.comprobante_path,
                    i.observacion,
                    u.name AS professor_name,
                    pao.title AS pao_name
                  FROM invoices i
                  LEFT JOIN users u ON i.professor_id = u.id
                  LEFT JOIN pao ON i.pao_id = pao.id";
        
        $params = [];
        $conditions = [];
        
        // Si es solo profesor, filtrar solo sus facturas
        if ($isOnlyProfessor && $userId !== null) {
            $conditions[] = "i.professor_id = :userId";
            $params[':userId'] = $userId;
        }
        
        // Filtrar por año si se proporciona
        if ($year !== null && $year !== '') {
            $conditions[] = "i.period_year = :year";
            $params[':year'] = $year;
        }
        
        // Agregar las condiciones WHERE si hay alguna
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY i.period_year DESC, i.period_month DESC, i.unit_number ASC, i.invoice_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId() {
        return $this->db->lastInsertId();
    }
}