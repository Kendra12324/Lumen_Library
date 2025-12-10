<?php
session_start();
require_once __DIR__ . '/../../model/Book.php';

$bookModel = new Book();

if (!isset($_GET['id'])) {
    header('Location: ViewArchived.php');
    exit;
}

$bookID = $_GET['id'];

$restored = $bookModel->restoreBook($bookID);

if ($restored) {
    
    $_SESSION['success_message'] = "Book ID $bookID has been restored successfully!";
    header('Location: ViewArchived.php');
    exit;
} else {
   
    $_SESSION['error_message'] = "Failed to restore book ID $bookID.";
    header('Location: ViewArchived.php');
    exit;
}
?>
