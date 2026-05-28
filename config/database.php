<?php
/**
 * ReserBot - Clase de conexión a base de datos
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Asegurar UTF-8 en la conexión
            $this->connection->exec("SET NAMES utf8mb4");
            $this->connection->exec("SET CHARACTER SET utf8mb4");

            // Alinear zona horaria de la sesión MySQL con la zona horaria de PHP.
            // Evita desfases entre NOW() en BD y date() en PHP.
            if (defined('APP_TIMEZONE')) {
                $tz = new DateTimeZone(APP_TIMEZONE);
                $now = new DateTime('now', $tz);
                $offsetSeconds = $tz->getOffset($now);
                $sign = $offsetSeconds >= 0 ? '+' : '-';
                $abs = abs($offsetSeconds);
                $hours = str_pad((string) floor($abs / 3600), 2, '0', STR_PAD_LEFT);
                $minutes = str_pad((string) floor(($abs % 3600) / 60), 2, '0', STR_PAD_LEFT);
                $mysqlTz = $sign . $hours . ':' . $minutes;
                $this->connection->exec("SET time_zone = '{$mysqlTz}'");
            }
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }
    
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollBack() {
        return $this->connection->rollBack();
    }
    
    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
