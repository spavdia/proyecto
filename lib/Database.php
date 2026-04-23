<?php
/**
 * P.Lluyot-2025
 */

namespace Sergio\Lib;

use mysqli;

// Incluimos la configuración (está en la misma carpeta 'lib')
require_once __DIR__ . '/db_credentials.php';

/**
 * Clase Database para gestionar la conexión y las operaciones con la base de datos usando MySQLi.
 * Proporciona métodos para ejecutar consultas preparadas de forma segura.
 */
class Database
{
    private $conn;
    public $error;

    /**
     * Constructor de la clase. Establece la conexión con la base de datos.
     * Si la conexión falla, termina la ejecución del script.
     */
    public function __construct()
    {
        $this->conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);

        if ($this->conn->connect_error) {
            die("Error fatal de conexión: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    /**
     * Ejecuta una consulta SELECT y devuelve los resultados.
     * Utiliza sentencias preparadas para prevenir inyección SQL.
     *
     * @param string $sql La consulta SQL con marcadores de posición (?).
     * @param array $params Un array con los parámetros para la consulta.
     * @return array Un array de arrays asociativos con los resultados.
     */
    public function executeQuery($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->error = $this->conn->error;
            return [];
        }

        // Si hay parámetros, los vincula a la consulta
        if ($params) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Obtiene todos los resultados como un array de arrays asociativos
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    /**
     * Ejecuta una consulta de modificación (INSERT, UPDATE, DELETE).
     * Utiliza sentencias preparadas para prevenir inyección SQL.
     *
     * @param string $sql La consulta SQL con marcadores de posición (?).
     * @param array $params Un array con los parámetros para la consulta.
     * @return int|false El número de filas afectadas en caso de éxito, o false si hubo un error.
     */
    public function executeUpdate($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->error = $this->conn->error;
            return false;
        }

        // Si hay parámetros, los vincula a la consulta
        if ($params) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        $success = $stmt->execute();

        if (!$success) {
            $this->error = $stmt->error;
            return false;
        }

        $filas = $stmt->affected_rows;
        $stmt->close();
        return $filas;
    }

    /**
     * Cierra la conexión con la base de datos.
     */
    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Determina los tipos de datos de un array de parámetros para usarlos en bind_param.
     *
     * @param array $params El array de parámetros.
     * @return string Una cadena con los tipos de datos (ej. "isd" para integer, string, double).
     */
    private function getParamTypes(array $params)
    {
        $types = "";
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= "i"; // Integer
            } elseif (is_double($param) || is_float($param)) {
                $types .= "d"; // Double / Float
            } else {
                $types .= "s"; // String (por defecto)
            }
        }
        return $types;
    }

    /**
     * Inicia una transacción.
     *
     * @return bool True si la transacción se inició correctamente, false en caso contrario.
     */
    public function beginTransaction()
    {
        return $this->conn->begin_transaction();
    }

    /**
     * Confirma la transacción actual.
     *
     * @return bool True si la confirmación fue exitosa, false en caso contrario.
     */
    public function commit()
    {
        return $this->conn->commit();
    }

    /**
     * Revierte la transacción actual.
     *
     * @return bool True si la reversión fue exitosa, false en caso contrario.
     */
    public function rollback()
    {
        return $this->conn->rollback();
    }
}
