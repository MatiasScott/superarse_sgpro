<?php
// app/models/UserModel.php

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected $table = "users";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fallback seguro cuando la tabla users no existe en la BD actual.
     */
    public function getAll()
    {
        try {
            return parent::getAll();
        } catch (PDOException $e) {
            error_log("UserModel::getAll fallback: " . $e->getMessage());
            return [];
        }
    }

    public function findByEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UserModel::findByEmail fallback: " . $e->getMessage());
            return null;
        }
    }

    public function create($name, $email, $password, $dedication = null, $escuela = null)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (name, email, password, dedicacion, escuela) VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);

            // Limpieza de datos
            $name = htmlspecialchars(strip_tags($name));
            $email = htmlspecialchars(strip_tags($email));
            $dedication = $dedication !== null ? htmlspecialchars(strip_tags((string) $dedication)) : null;
            $escuela = $escuela !== null ? htmlspecialchars(strip_tags((string) $escuela)) : null;
            // La contraseña ya viene hasheada desde el controlador, no hashear nuevamente

            // Usar execute con array es más confiable
            if ($stmt->execute([$name, $email, $password, $dedication, $escuela])) {
                return true;
            }

            return false;
        } catch (PDOException $e) {
            error_log("UserModel::create fallback: " . $e->getMessage());
            return false;
        }
    }

    // Método para obtener el ID del último registro insertado
    public function getLastInsertedId()
    {
        return $this->db->lastInsertId();
    }

    public function update($id, $name, $email, $dedication = null, $escuela = null)
    {
        try {
            $query = "UPDATE " . $this->table . " SET name = ?, email = ?, dedicacion = ?, escuela = ? WHERE id = ?";

            $stmt = $this->db->prepare($query);

            // Limpieza de datos
            $name = htmlspecialchars(strip_tags($name));
            $email = htmlspecialchars(strip_tags($email));
            $dedication = $dedication !== null ? htmlspecialchars(strip_tags((string) $dedication)) : null;
            $escuela = $escuela !== null ? htmlspecialchars(strip_tags((string) $escuela)) : null;

            return $stmt->execute([$name, $email, $dedication, $escuela, $id]);
        } catch (PDOException $e) {
            error_log("UserModel::update fallback: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("UserModel::delete fallback: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tablas que referencian a users pero que no constituyen "datos" del usuario
     * (roles, bitácoras, notificaciones). No impiden la eliminación física.
     */
    private const NON_BLOCKING_RELATED_TABLES = [
        'user_roles_pivot',
        'activity_logs',
        'audit_logs',
        'notifications',
    ];

    /**
     * Devuelve [tabla => cantidad] de registros que referencian al usuario
     * (portafolio, CV, contratos, facturas, evaluaciones, asignaciones, etc.).
     * Las FK se leen de information_schema para cubrir cualquier tabla nueva.
     * Devuelve null si no se pudo verificar (el llamador debe asumir que hay datos).
     */
    public function getRelatedDataSummary($id)
    {
        try {
            $fkStmt = $this->db->prepare(
                "SELECT TABLE_NAME, COLUMN_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
                   AND REFERENCED_TABLE_NAME = ?
                   AND REFERENCED_COLUMN_NAME = 'id'"
            );
            $fkStmt->execute([$this->table]);
            $references = $fkStmt->fetchAll(PDO::FETCH_ASSOC);

            $summary = [];
            foreach ($references as $ref) {
                $tableName = $ref['TABLE_NAME'];
                $columnName = $ref['COLUMN_NAME'];

                if (in_array($tableName, self::NON_BLOCKING_RELATED_TABLES, true)) {
                    continue;
                }

                $countStmt = $this->db->prepare(
                    "SELECT COUNT(*) FROM `" . str_replace('`', '``', $tableName) . "` WHERE `" . str_replace('`', '``', $columnName) . "` = ?"
                );
                $countStmt->execute([$id]);
                $count = (int) $countStmt->fetchColumn();

                if ($count > 0) {
                    $summary[$tableName] = ($summary[$tableName] ?? 0) + $count;
                }
            }

            return $summary;
        } catch (PDOException $e) {
            error_log("UserModel::getRelatedDataSummary error: " . $e->getMessage());
            return null;
        }
    }

    public function setActive($id, $active)
    {
        try {
            $stmt = $this->db->prepare("UPDATE " . $this->table . " SET active = ? WHERE id = ?");
            return $stmt->execute([$active ? 1 : 0, $id]);
        } catch (PDOException $e) {
            error_log("UserModel::setActive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminación física (roles + usuario) en una transacción.
     * Solo debe usarse cuando el usuario no tiene datos relacionados.
     */
    public function hardDelete($id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM user_roles_pivot WHERE user_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("UserModel::hardDelete error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $hashedPassword)
    {
        try {
            $query = "UPDATE " . $this->table . " SET password = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$hashedPassword, $id]);
        } catch (PDOException $e) {
            error_log("UserModel::updatePassword fallback: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra last_login_at con NOW() en la BD configurada.
     * Requiere sp_registrar_ultimo_acceso y la columna users.last_login_at.
     * Devuelve false y registra el error si falla, sin bloquear la autenticacion.
     */
    public function registerLastLogin($userId)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_registrar_ultimo_acceso(?)");
            try {
                $stmt->execute([$userId]);
            } finally {
                // Liberar los resultados del procedimiento antes de reutilizar la conexion.
                $stmt->closeCursor();
            }
            return true;
        } catch (PDOException $e) {
            error_log("UserModel::registerLastLogin error: " . $e->getMessage());
            return false;
        }
    }

    public function getUsersByRole($roleName)
    {
        // Corregida la consulta para unirse a la tabla de roles y usar la columna 'role_name'
        $query = "SELECT u.id, u.name 
              FROM users u 
              JOIN user_roles_pivot urp ON u.id = urp.user_id 
              JOIN user_roles r ON urp.role_id = r.id 
              WHERE r.role_name = ?";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $roleName);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UserModel::getUsersByRole fallback: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene usuarios con un rol específico (incluye completo)
     */
    public function getUsersWithRole($roleName)
    {
        $query = "SELECT DISTINCT u.id, u.name, u.email, u.dedicacion, u.active 
              FROM users u 
              JOIN user_roles_pivot urp ON u.id = urp.user_id 
              JOIN user_roles r ON urp.role_id = r.id 
              WHERE r.role_name = ? AND u.active = 1
              ORDER BY u.name";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$roleName]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UserModel::getUsersWithRole fallback: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene usuarios sin un rol específico (excepto)
     */
    public function getUsersWithoutRole($roleName)
    {
        $query = "SELECT u.id, u.name, u.email 
                  FROM users u 
                  WHERE u.active = 1 AND u.id NOT IN (
                      SELECT DISTINCT u2.id 
                      FROM users u2
                      JOIN user_roles_pivot urp ON u2.id = urp.user_id 
                      JOIN user_roles r ON urp.role_id = r.id 
                      WHERE r.role_name = ?
                  )
                  ORDER BY u.name";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$roleName]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UserModel::getUsersWithoutRole fallback: " . $e->getMessage());
            return [];
        }
    }
}
