<?php
// app/models/ProfessorCvModel.php

require_once __DIR__ . '/BaseModel.php';

class ProfessorCvModel extends BaseModel
{
    protected $table = "professor_cv";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $surnames,
        $firstName,
        $cedulaPassport,
        $nationality = null,
        $birthDate = null,
        $city = null,
        $address = null,
        $phone = null,
        $cellPhone = null,
        $email,
        $photoPath = null
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, surnames, first_name, cedula_passport, nationality, 
            birth_date, city, address, phone, cell_phone, email, photo_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $surnames);
        $stmt->bindParam(3, $firstName);
        $stmt->bindParam(4, $cedulaPassport);
        $stmt->bindParam(5, $nationality);
        $stmt->bindParam(6, $birthDate);
        $stmt->bindParam(7, $city);
        $stmt->bindParam(8, $address);
        $stmt->bindParam(9, $phone);
        $stmt->bindParam(10, $cellPhone);
        $stmt->bindParam(11, $email);
        $stmt->bindParam(12, $photoPath);

        return $stmt->execute();
    }

    public function update(
        $professorId,
        $surnames,
        $firstName,
        $cedulaPassport,
        $nationality,
        $birthDate,
        $city,
        $address,
        $phone,
        $cellPhone,
        $email,
        $photoPath
    ) {
        if (!is_null($photoPath)) {
            // Actualiza todos los campos, incluyendo la foto
            $query = "UPDATE {$this->table} SET
            surnames = ?, first_name = ?, cedula_passport = ?, nationality = ?, 
            birth_date = ?, city = ?, address = ?, phone = ?, cell_phone = ?, 
            email = ?, photo_path = ?
        WHERE professor_id = ?";

            $stmt = $this->db->prepare($query);

            return $stmt->execute([
                $surnames,
                $firstName,
                $cedulaPassport,
                $nationality,
                $birthDate,
                $city,
                $address,
                $phone,
                $cellPhone,
                $email,
                $photoPath,
                $professorId // Usar professorId en la cláusula WHERE
            ]);
        } else {
            // Actualiza todos los campos excepto la foto
            $query = "UPDATE {$this->table} SET
            surnames = ?, first_name = ?, cedula_passport = ?, nationality = ?, 
            birth_date = ?, city = ?, address = ?, phone = ?, cell_phone = ?, 
            email = ?
        WHERE professor_id = ?";

            $stmt = $this->db->prepare($query);

            return $stmt->execute([
                $surnames,
                $firstName,
                $cedulaPassport,
                $nationality,
                $birthDate,
                $city,
                $address,
                $phone,
                $cellPhone,
                $email,
                $professorId // Usar professorId en la cláusula WHERE
            ]);
        }
    }

    public function findByProfessorId($professorId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE professor_id = ?");
        $stmt->execute([$professorId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
