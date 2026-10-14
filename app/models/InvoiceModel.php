<?php
// app/models/InvoiceModel.php

require_once __DIR__ . '/BaseModel.php';

class DuplicateInvoiceException extends RuntimeException {}

class InvoiceModel extends BaseModel {
    protected $table = "invoices";
    private $lastInsertedId;

    public function __construct() {
        parent::__construct();
    }

    public function canUploadInvoice($id) {
        $stmt = $this->db->prepare("SELECT invoice_upload_enabled FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $value = $stmt->fetchColumn();
        if ($value === false) {
            throw new RuntimeException('Factura no encontrada al verificar la habilitación.');
        }
        return (int) $value === 1;
    }

    public function setInvoiceUploadPermission($id, $enabled) {
        if (!in_array($enabled, [0, 1], true)) {
            throw new InvalidArgumentException('La habilitación debe ser 0 o 1.');
        }
        $stmt = $this->db->prepare("UPDATE invoices SET invoice_upload_enabled = ? WHERE id = ?");
        return $stmt->execute([$enabled, $id]);
    }

    public function getInvoicedUnitsByProfessor($professorId = null, $excludeInvoiceId = null) {
        $query = "SELECT DISTINCT professor_id, pao_id, unit_number FROM {$this->table} WHERE 1 = 1";
        $params = [];
        if ($professorId !== null) {
            $query .= " AND professor_id = ?";
            $params[] = $professorId;
        }
        if ($excludeInvoiceId !== null) {
            $query .= " AND id <> ?";
            $params[] = $excludeInvoiceId;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $units = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $units[(int) $row['professor_id']][(int) $row['pao_id']][] = (int) $row['unit_number'];
        }
        return $units;
    }

    public function hasInvoiceForUnit($professorId, $paoId, $unitNumber, $excludeInvoiceId = null) {
        $query = "SELECT id FROM {$this->table} WHERE professor_id = ? AND pao_id = ? AND unit_number = ?";
        $params = [$professorId, $paoId, $unitNumber];
        if ($excludeInvoiceId !== null) {
            $query .= " AND id <> ?";
            $params[] = $excludeInvoiceId;
        }
        $query .= " LIMIT 1";
        if ($this->db->inTransaction()) {
            $query .= " FOR UPDATE";
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() !== false;
    }

    private function saveUniqueUnit($professorId, $paoId, $unitNumber, $excludeInvoiceId, callable $save) {
        $this->db->beginTransaction();
        try {
            // Serializar las escrituras del mismo profesor, incluso entre solicitudes simultaneas.
            $lock = $this->db->prepare("SELECT id FROM users WHERE id = ? FOR UPDATE");
            $lock->execute([$professorId]);
            if ($professorId !== null && $lock->fetchColumn() === false) {
                throw new RuntimeException('Profesor no encontrado al guardar la factura.');
            }
            if ($this->hasInvoiceForUnit($professorId, $paoId, $unitNumber, $excludeInvoiceId)) {
                throw new DuplicateInvoiceException('Ya existe una factura para este profesor, PAO y unidad. No puede facturar la misma unidad nuevamente.');
            }
            $saved = $save();
            if (!$saved) {
                $this->db->rollBack();
                return false;
            }
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
    
    public function create($professorId, $paoId, $unitNumber, $periodMonth, $periodYear, $invoiceDate, $amount, $status, $paymentProofPath = null, $comprobantePath = null, $observacion = null) {
        $query = "INSERT INTO " . $this->table . " (professor_id, pao_id, unit_number, period_month, period_year, invoice_date, amount, status, payment_proof_path, comprobante_path, observacion, invoice_upload_enabled) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
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
        return $this->saveUniqueUnit($professorId, $paoId, $unitNumber, null, function () use ($stmt) {
            $saved = $stmt->execute();
            if ($saved) {
                $this->lastInsertedId = $this->db->lastInsertId();
            }
            return $saved;
        });
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
        $invoice = $this->find($id);
        if (!$invoice) {
            throw new RuntimeException('Factura no encontrada.');
        }
        return $this->saveUniqueUnit($invoice['professor_id'], $invoice['pao_id'], $unitNumber, $id, function () use ($stmt) {
            return $stmt->execute();
        });
    }

    public function updateProfessorAttachment($id, $professorId, $paymentProofPath, $observacion) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET payment_proof_path = ?, observacion = ? WHERE id = ? AND professor_id = ?"
        );
        return $stmt->execute([$paymentProofPath, $observacion, $id, $professorId]);
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
        return $this->lastInsertedId ?? $this->db->lastInsertId();
    }
}