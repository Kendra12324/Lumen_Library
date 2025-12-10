<?php
require_once __DIR__ . '/../config/Database.php';

class Penalty {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getConnection() {
        return $this->conn;
    }


    public function getTotalUnpaidFines($user_id) {
     
        $sql = "SELECT SUM(p.Amount) as total 
                FROM penalty p 
                JOIN borrow b ON p.Borrow_ID = b.Borrow_ID 
                WHERE b.User_ID = ? AND p.Status = 'Unpaid'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
  
        return $result['total'] ?? 0;
    }

    public function getAllPenalties() {
        $sql = "SELECT 
                    p.Penalty_ID,
                    b.Borrow_ID,
                    u.Name AS User_Name,
                    bk.Title AS Book_Title,
                    p.Amount,
                    p.Status
                FROM Penalty p
                JOIN Borrow b ON p.Borrow_ID = b.Borrow_ID
                JOIN User u ON b.User_ID = u.User_ID
                JOIN Book bk ON b.Book_ID = bk.Book_ID
                ORDER BY p.Penalty_ID DESC";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsPaid($penaltyID) {
        $sql = "UPDATE Penalty SET Status = 'Paid' WHERE Penalty_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $penaltyID);
        $stmt->execute();
    }

    public function deletePenalty($penaltyID) {
        $sql = "DELETE FROM Penalty WHERE Penalty_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $penaltyID);
        $stmt->execute();
    }
}
?>
