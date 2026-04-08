<?php
// app/models/BaseModel.php

require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Nuevo método para obtener la conexión
    public function getConnection()
    {
        return $this->db;
    }

    // Método para obtener todos los registros de una tabla
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para encontrar un registro por ID
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * Obtiene todos los registros de la tabla para un profesor específico.
     *
     * @param int $professorId
     * @return array
     */
    public function getAllByProfessorId($professorId)
    {
        $query = "SELECT * FROM {$this->table} WHERE professor_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$professorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
