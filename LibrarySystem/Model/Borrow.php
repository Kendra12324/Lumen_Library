<?php
require_once __DIR__ . '/../config/Database.php';

class Borrow {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }
    
    public function getConnection() {
        return $this->conn;
    }

    public function getAllBorrowRecords() {
        $sql = "SELECT 
                    b.Borrow_ID,
                    u.Name AS User_Name,
                    bk.Title AS Book_Title,
                    b.Borrow_Date,
                    b.Due_Date,
                    b.Status
                FROM Borrow b
                JOIN User u ON b.User_ID = u.User_ID
                JOIN Book bk ON b.Book_ID = bk.Book_ID
                ORDER BY b.Borrow_ID DESC";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check how many active borrows a student currently has
    public function getActiveBorrowCount($userID) {
    $sql = "SELECT COUNT(*) AS total 
            FROM Borrow 
            WHERE User_ID = ? 
              AND (Status = 'Borrowed' OR Status = 'Overdue')";
              
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'];
}


    // Borrow a new book (with validation: max 3)
    public function borrowBook($userID, $bookID) {
     
        $activeCount = $this->getActiveBorrowCount($userID);
        if ($activeCount >= 3) {
            return [
                'success' => false,
                'message' => 'Borrow limit reached. Students can only borrow up to 3 books per semester.'
            ];
        }

        // Set dates (14-day default)
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        $status = 'Borrowed';

        $sql = "INSERT INTO Borrow (User_ID, Book_ID, Borrow_Date, Due_Date, Status)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $userID, $bookID, $borrowDate, $dueDate, $status);

        if ($stmt->execute()) {
            // Decrease available copies
            $this->updateBookCopies($bookID, -1);
            return [
                'success' => true,
                'message' => 'Book successfully borrowed!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to borrow book.'
            ];
        }
    }

    //  Update book copies after borrow/return
    private function updateBookCopies($bookID, $change) {
        $sql = "UPDATE Book SET Copies_Available = Copies_Available + ? WHERE Book_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $change, $bookID);
        $stmt->execute();
    }

    public function markReturned($borrowID) {
        
        $sql = "UPDATE Borrow SET Status = 'Returned', Return_Date = CURDATE() WHERE Borrow_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $borrowID);
        $stmt->execute();
        $this->restoreBookCopy($borrowID);

        return true;
    }

    private function restoreBookCopy($borrowID) {
        $sql = "SELECT Book_ID FROM Borrow WHERE Borrow_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $borrowID);
        $stmt->execute();
        $bookID = $stmt->get_result()->fetch_assoc()['Book_ID'];
        $this->updateBookCopies($bookID, 1);
    }

    // Renew borrow (extend due date by 7 days)
    public function renewBorrow($borrowID) {
        $sql = "UPDATE Borrow 
                SET Due_Date = DATE_ADD(Due_Date, INTERVAL 7 DAY),
                    Renewal_Count = COALESCE(Renewal_Count, 0) + 1
                WHERE Borrow_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $borrowID);
        return $stmt->execute();
    }

    //  Apply penalty (for lost or unreturned books)
   public function applyPenalty($borrowID, $amount = 50)
{
    // Check if penalty already exists for this borrow
    $sqlCheck = "SELECT COUNT(*) as cnt FROM Penalty WHERE Borrow_ID = ?";
    $stmtCheck = $this->conn->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $borrowID);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result()->fetch_assoc();
    if ($resultCheck['cnt'] > 0) {
       
        return false;
    }

   
    $sql = "SELECT bk.Book_ID
            FROM Borrow b 
            JOIN Book bk ON b.Book_ID = bk.Book_ID
            WHERE b.Borrow_ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $borrowID);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();

    if (!$book) return false;

    // Insert penalty using fixed amount
    $sql2 = "INSERT INTO Penalty (Borrow_ID, Amount, Status)
             VALUES (?, ?, 'Unpaid')";
    $stmt2 = $this->conn->prepare($sql2);
    $stmt2->bind_param("id", $borrowID, $amount);
    return $stmt2->execute();
}


    //  Placeholder (future email reminder)
    public function sendReminder($borrowID) {
        return true;
    }

    //  Automatically update overdue status
    public function updateOverdueStatus() {
    $today = date('Y-m-d');
    $sql = "UPDATE Borrow 
            SET Status = 'Overdue' 
            WHERE Status = 'Borrowed' 
              AND Due_Date < ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    }

    public function countAllBorrows() {
    $result = $this->conn->query("SELECT COUNT(*) AS total FROM Borrow");
    $row = $result->fetch_assoc();
    return $row['total'];
}

public function countActiveBorrows() {
    $result = $this->conn->query("SELECT COUNT(*) AS total FROM Borrow WHERE Status='Borrowed'");
    $row = $result->fetch_assoc();
    return $row['total'];
}


public function markLost($borrowID) {
    $sql = "UPDATE Borrow SET Status = 'Lost' WHERE Borrow_ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $borrowID);
    return $stmt->execute();
}

    // Mark a borrow as Overdue
    public function markOverdue($borrowID) {
    $sql = "UPDATE Borrow SET Status = 'Overdue' WHERE Borrow_ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $borrowID);
    $result = $stmt->execute();
    $this->applyPenalty($borrowID, 50); 
    return $result;
    }

    // NEW FUNCTION: Teacher Specific 
    public function borrowBookUnlimited($userID, $bookID) {


        // Set dates (14-day default)
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        $status = 'Borrowed';

        $sql = "INSERT INTO Borrow (User_ID, Book_ID, Borrow_Date, Due_Date, Status)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $userID, $bookID, $borrowDate, $dueDate, $status);

        if ($stmt->execute()) {
            // Decrease available copies
            $this->updateBookCopies($bookID, -1);
            
            return [
                'success' => true,
                'message' => 'Book successfully borrowed!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to borrow book.'
            ];
        }
    }

    //  Get Recent Borrow Activities (Limit 5)
    public function getRecentActivities() {
        $sql = "SELECT u.Name, bk.Title, b.Status, b.Borrow_Date 
                FROM Borrow b
                JOIN User u ON b.User_ID = u.User_ID
                JOIN Book bk ON b.Book_ID = bk.Book_ID
                ORDER BY b.Borrow_Date DESC LIMIT 5";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //  Get Overdue Alerts (Limit 5)
    public function getOverdueAlerts() {
        $sql = "SELECT u.Name, bk.Title, b.Due_Date 
                FROM Borrow b
                JOIN User u ON b.User_ID = u.User_ID
                JOIN Book bk ON b.Book_ID = bk.Book_ID
                WHERE b.Status = 'Overdue' OR (b.Status = 'Borrowed' AND b.Due_Date < CURDATE())
                LIMIT 5";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }



}
?>
