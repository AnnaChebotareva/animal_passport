<?php
class Database {
    private $host = "localhost";
    private $db_name = "animal_passport_system";
    private $username = "root";      
    private $password = "";          
    public $conn;

    // Метод для получения соединения с БД
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Создаем PDO соединение
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            
            // Устанавливаем режим ошибок (выбрасываем исключения)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Для корректной работы с русскими символами
            $this->conn->exec("SET NAMES 'utf8mb4'");
            $this->conn->exec("SET CHARACTER SET utf8mb4");
            
        } catch(PDOException $exception) {
            die("Ошибка подключения к БД: " . $exception->getMessage());
        }
        
        return $this->conn;
    }
    
    // Метод для безопасного выполнения запросов (защита от SQL-инъекций)
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }
}
?>