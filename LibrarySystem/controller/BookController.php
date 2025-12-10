<?php
session_start();
require_once __DIR__ . '/../model/Book.php';

$bookModel = new Book();

// ✅ ADD BOOK
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $copies_total = $_POST['copies_total'];
    $copies_available = $_POST['copies_available'];
    $status = $_POST['status'];
    $category = $_POST['category'];
    $publisher = $_POST['publisher'];
    $year = $_POST['year'];
    $location = $_POST['location'];

    if ($bookModel->addBook($title, $author, $isbn, $copies_total, $copies_available, $status, $category, $publisher, $year, $location)) {
        header("Location: ../view/librarian/books.php?msg=BookAdded");
    } else {
        header("Location: ../view/librarian/books.php?error=AddFailed");
    }
    exit;
}

// ✅ UPDATE BOOK
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $copies_total = $_POST['copies_total'];
    $copies_available = $_POST['copies_available'];
    $status = $_POST['status'];
    $archived = isset($_POST['archived']) ? 1 : 0;
    $category = $_POST['category'];
    $publisher = $_POST['publisher'];
    $year = $_POST['year'];
    $location = $_POST['location'];

    if ($bookModel->updateBook($book_id, $title, $author, $isbn, $copies_total, $copies_available, $status, $archived, $category, $publisher, $year, $location)) {
        header("Location: ../view/librarian/books.php?msg=BookUpdated");
    } else {
        header("Location: ../view/librarian/books.php?error=UpdateFailed");
    }
    exit;
}

// ✅ DELETE BOOK
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];
    if ($bookModel->deleteBook($book_id)) {
        header("Location: ../view/librarian/books.php?msg=BookDeleted");
    } else {
        header("Location: ../view/librarian/books.php?error=DeleteFailed");
    }
    exit;
}

// ✅ ARCHIVE BOOK
if (isset($_GET['archive'])) {
    $book_id = $_GET['archive'];
    $book = $bookModel->getBookById($book_id);
    if ($book) {
        $bookModel->updateBook(
            $book_id,
            $book['Title'],
            $book['Author'],
            $book['ISBN'],
            $book['Copies_Total'],
            $book['Copies_Available'],
            $book['Status'],
            1, // ✅ set Archived to true
            $book['Category'],
            $book['Publisher'],
            $book['Year'],
            $book['Location']
        );
    }
    header("Location: ../view/librarian/ManageBooks.php?msg=BookArchived");
    exit;
}

?>
