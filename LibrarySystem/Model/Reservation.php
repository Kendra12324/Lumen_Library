<?php
require_once __DIR__ . '/../config/Database.php';

class Reservation {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn; 
    }


    public function getAllReservations() {
        $sql = "
            SELECT 
                r.Reservation_ID,
                u.Name AS User_Name,
                b.Title AS Book_Title,
                r.Reservation_Date,
                r.Expiry_Date,
                r.Status,
                r.Fulfilled_By_Borrow_ID
            FROM Reservation r
            JOIN User u ON r.User_ID = u.User_ID
            JOIN Book b ON r.Book_ID = b.Book_ID
            ORDER BY r.Reservation_Date DESC
        ";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    // Count active reservations for a specific user
public function countActiveReservationsByUser($user_id) {
    $sql = "SELECT COUNT(*) AS total FROM Reservation 
            WHERE User_ID = ? AND Status IN ('Pending', 'Approved')";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'];
}

// Permanently delete reservation
public function deleteReservation($reservation_id) {
    $sql = "DELETE FROM Reservation WHERE Reservation_ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    return $stmt->execute();
}


    // Cancel reservation
    public function cancelReservation($reservation_id){
        $sql = "UPDATE reservation SET Status = 'Cancelled' WHERE Reservation_ID = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$reservation_id]);
    }
    // Approve reservation
public function approveReservation($reservation_id) {
    $sql = "UPDATE reservation SET Status = 'Approved' WHERE Reservation_ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    return $stmt->execute();
}



    // Mark reservation as fulfilled
    public function fulfillReservation($reservation_id, $borrow_id) {
        $sql = "UPDATE Reservation 
                SET Status = 'Fulfilled', Fulfilled_By_Borrow_ID = ?
                WHERE Reservation_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $borrow_id, $reservation_id);
        return $stmt->execute();
    }

    // Add new reservation
    public function addReservation($user_id, $book_id, $reservation_date, $expiry_date, $status) {
        $sql = "INSERT INTO Reservation (User_ID, Book_ID, Reservation_Date, Expiry_Date, Status)
            VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $book_id, $reservation_date, $expiry_date, $status]);
    }

    // Get reservations for a specific user
    public function getReservationsByUser($user_id) {
    $sql = "
        SELECT 
            r.Reservation_ID,
            b.Title AS Book_Title,
            r.Reservation_Date,
            r.Expiry_Date,
            r.Status
        FROM Reservation r
        JOIN Book b ON r.Book_ID = b.Book_ID
        WHERE r.User_ID = ?
        ORDER BY r.Reservation_Date DESC
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAllReservations() {
    $result = $this->conn->query("SELECT COUNT(*) AS total FROM Reservation");
    $row = $result->fetch_assoc();
    return $row['total'];
}

}
?>
