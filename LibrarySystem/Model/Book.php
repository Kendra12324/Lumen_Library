<?php
require_once __DIR__ . '/../config/Database.php';

class Book extends Database {

    public function __construct() {
        parent::__construct();
    }

    // ✅ Add book (auto ID)
    public function addBook($title, $author, $isbn, $copies, $category, $publisher, $year, $location) {
        $query = "INSERT INTO book (Title, Author, ISBN, Copies_Available, Category, Publisher, Year, Location, Status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssissis", $title, $author, $isbn, $copies, $category, $publisher, $year, $location);
        return $stmt->execute();
    }

    // ✅ Add book (manual ID)
    public function addBookManual($book_id, $title, $author, $isbn, $copies, $category, $publisher, $year, $location) {
        $query = "INSERT INTO book (Book_ID, Title, Author, ISBN, Copies_Available, Category, Publisher, Year, Location, Status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssissis", $book_id, $title, $author, $isbn, $copies, $category, $publisher, $year, $location);
        return $stmt->execute();
    }

    // ✅ Fetch all books (includes archived)
    public function getAllBooks() {
        $query = "SELECT * FROM book";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ✅ Get only active (non-archived) books
    public function getActiveBooks() {
        $query = "SELECT * FROM book WHERE Status != 'Archived'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ✅ Fetch one book by ID
    public function getBookById($book_id) {
        $query = "SELECT * FROM book WHERE Book_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function updateBook($book_id, $title, $author, $copies_total, $copies_available, $category, $publisher, $year, $location) {
        $query = "UPDATE book 
                  SET Title = ?, 
                      Author = ?, 
                      Copies_Total = ?, 
                      Copies_Available = ?, 
                      Category = ?, 
                      Publisher = ?, 
                      Year = ?, 
                      Location = ? 
                  WHERE Book_ID = ?";
                  
        $stmt = $this->conn->prepare($query);
 
        $stmt->bind_param("ssiissssi", $title, $author, $copies_total, $copies_available, $category, $publisher, $year, $location, $book_id);
        return $stmt->execute();
    }

    // ✅ Delete book by ID
    public function deleteBook($book_id) {
        $query = "DELETE FROM book WHERE Book_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        return $stmt->execute();
    }

    // ✅ Archive book
    public function archiveBook($book_id) {
        $query = "UPDATE book SET Status = 'Archived' WHERE Book_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        return $stmt->execute();
    }

    // ✅ Restore book
    public function restoreBook($book_id) {
        $query = "UPDATE book SET Status = 'Available' WHERE Book_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        return $stmt->execute();
    }

    //  Get all archived books
    public function getArchivedBooks() {
        $query = "SELECT * FROM book WHERE Status = 'Archived'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function countBooks() {
    $result = $this->conn->query("SELECT COUNT(*) AS total FROM book");
    $row = $result->fetch_assoc();
    return $row['total'];
}


    public function searchBooks($keyword) {
        $term = "%" . $keyword . "%"; 

        $query = "SELECT * FROM book 
                  WHERE Status != 'Archived' 
                  AND (Title LIKE ? OR Author LIKE ? OR Category LIKE ? OR ISBN LIKE ?)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $term, $term, $term, $term);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}
?>
