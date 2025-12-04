<?php
/**
 * Classe de Conexão com o Banco de Dados
 * Utiliza PDO para conexão segura
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $conn;
    
    /**
     * Construtor privado (Singleton)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
    
    /**
     * Obtém a instância única da classe (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retorna a conexão PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Executa uma query SELECT
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Erro na query: " . $e->getMessage());
        }
    }
    
    /**
     * Executa uma query SELECT e retorna apenas uma linha
     */
    public function querySingle($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Erro na query: " . $e->getMessage());
        }
    }
    
    /**
     * Executa INSERT, UPDATE ou DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            throw new Exception("Erro ao executar: " . $e->getMessage());
        }
    }
    
    /**
     * Retorna o último ID inserido
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Desfaz uma transação
     */
    public function rollback() {
        return $this->conn->rollback();
    }
    
    /**
     * Previne clonagem da instância
     */
    private function __clone() {}
    
    /**
     * Previne desserialização da instância
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Função auxiliar para obter a conexão
function getDB() {
    return Database::getInstance();
}
