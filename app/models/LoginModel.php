<?php

declare(strict_types=1);

namespace Sergio\App\models;
use Sergio\Lib\Database;

class LoginModel{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, nombre, email, password_hash, rol, activo
                FROM usuarios
                WHERE email = ?
                LIMIT 1";

        $resultados = $this->db->executeQuery($sql, [$email]);

        return !empty($resultados) ? $resultados[0] : null;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nombre, email, rol, activo, created_at
                FROM usuarios
                WHERE id = ?
                LIMIT 1";

        $resultados = $this->db->executeQuery($sql, [$id]);

        return !empty($resultados) ? $resultados[0] : null;
    }
}
