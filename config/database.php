<?php
class Database {
    private $server = "localhost";
    private $database = "productos_medicos";
    private $username = "admin_farmacia";
    private $password = "#Comer@PROD_medicos#";
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("sqlsrv:Server={$this->server};Database={$this->database}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function executeProcedure($procedureName, $params = []) {
        try {
            // Construir la consulta SQL con parámetros nombrados
            $paramNames = [];
            $paramValues = [];
            
            foreach ($params as $key => $value) {
                $paramName = ":p" . ($key + 1);
                $paramNames[] = $paramName;
                $paramValues[$paramName] = $value;
            }
            
            $paramString = implode(', ', $paramNames);
            $sql = "EXEC $procedureName $paramString";
            
            $stmt = $this->conn->prepare($sql);
            
            // Vincular parámetros
            foreach ($paramValues as $name => $value) {
                $stmt->bindValue($name, $value);
            }
            
            $stmt->execute();
            
            // Manejar múltiples resultsets
            $results = [];
            do {
                if ($stmt->columnCount() > 0) {
                    $results = array_merge($results, $stmt->fetchAll());
                }
            } while ($stmt->nextRowset());
            
            return $results;
            
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Método específico para procedimientos que retornan un solo valor (como ID)
    public function executeProcedureSingle($procedureName, $params = []) {
        try {
            $result = $this->executeProcedure($procedureName, $params);
            
            if (isset($result['error'])) {
                return $result;
            }
            
            // Retornar el primer resultado si existe
            return !empty($result) ? $result[0] : ['success' => true];
            
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Método para procedimientos que no retornan resultados
    public function executeProcedureNoResult($procedureName, $params = []) {
        try {
            $result = $this->executeProcedure($procedureName, $params);
            
            if (isset($result['error'])) {
                return $result;
            }
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Método para consultas SELECT simples
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>