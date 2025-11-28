<?php
class Database {
    private $server = "localhost";
    private $database = "productos_medicos";
    private $username = "tu_usuario";
    private $password = "tu_password";
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("sqlsrv:Server={$this->server};Database={$this->database}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function executeProcedure($procedureName, $params = []) {
        try {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $sql = "EXEC $procedureName $placeholders";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>