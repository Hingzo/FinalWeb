<?php
// backend/models/BaseModel.php
class BaseModel
{
    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli('localhost', 'root', '', 'db_le.gicaft');
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    public function __destruct()
    {
        $this->conn->close();
    }

    // Truy vấn an toàn với prepared statements
    public function prepareAndExecute($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Lỗi prepare: " . $this->conn->error);
        }
        if ($params) {
            $types = str_repeat('s', count($params)); // Giả định params là string
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    // Lấy tất cả bản ghi
    public function fetchAll($sql, $params = [])
    {
        $result = $this->prepareAndExecute($sql, $params);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Lấy một bản ghi
    public function fetchOne($sql, $params = [])
    {
        $result = $this->prepareAndExecute($sql, $params);
        return $result->fetch_assoc();
    }

    // Truy vấn thô (dùng khi cần, nhưng tránh SQL Injection)
    public function query($sql)
    {
        return $this->conn->query($sql);
    }
}