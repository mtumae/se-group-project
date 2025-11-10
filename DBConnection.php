<?php
class Database {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $conn;

    public function __construct($config) {
        $this->host = $config['db_host'];
        $this->db   = $config['db_name'];
        $this->user = $config['db_user'];
        $this->pass = $config['db_pass'];
    }

    
    public function connect() {
        if ($this->conn == null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db}",
                    $this->user,
                    $this->pass
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }


    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->conn->lastInsertId();
    }
    public function update($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }

    
    public function delete($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }
}
