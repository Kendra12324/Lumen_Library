<?php
require_once __DIR__ . '/../config/Database.php';

class BorrowingRecord {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function addRecord($borrowID, $action, $notes, $changedBy) {
        $sql = "INSERT INTO borrowing_record (Borrow_ID, Action_Type, Notes, Changed_By)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issi", $borrowID, $action, $notes, $changedBy);
        return $stmt->execute();
    }
}
