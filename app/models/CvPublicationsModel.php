<?php
// app/models/CvPublicationsModel.php

require_once __DIR__ . '/BaseModel.php';

class CvPublicationsModel extends BaseModel
{
    protected $table = "cv_publications";

    public function __construct()
    {
        parent::__construct();
    }

    public function create(
        $professorId,
        $productionType,
        $publicationTitle,
        $publisherMagazine,
        $isbnIssn,
        $authorship
    ) {
        $query = "INSERT INTO {$this->table} (
            professor_id, production_type, publication_title, publisher_magazine, isbn_issn, authorship
        ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $professorId);
        $stmt->bindParam(2, $productionType);
        $stmt->bindParam(3, $publicationTitle);
        $stmt->bindParam(4, $publisherMagazine);
        $stmt->bindParam(5, $isbnIssn);
        $stmt->bindParam(6, $authorship);

        return $stmt->execute();
    }

    public function findById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(
        $id,
        $productionType,
        $publicationTitle,
        $publisherMagazine,
        $isbnIssn,
        $authorship
    ) {
        $query = "UPDATE {$this->table} SET 
            production_type = ?,
            publication_title = ?,
            publisher_magazine = ?,
            isbn_issn = ?,
            authorship = ?
        WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $productionType);
        $stmt->bindParam(2, $publicationTitle);
        $stmt->bindParam(3, $publisherMagazine);
        $stmt->bindParam(4, $isbnIssn);
        $stmt->bindParam(5, $authorship);
        $stmt->bindParam(6, $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}