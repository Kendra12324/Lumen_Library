<?php
session_start();
require_once __DIR__ . '/../../model/Book.php';
$bookModel = new Book();


if (!isset($_GET['id'])) {
    header('Location: ManageBooks.php');
    exit;
}


$book = $bookModel->getBookById($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
      
        
        $bookModel->updateBook(
            $_GET['id'],
            $_POST['title'],
            $_POST['author'],
            $_POST['copies_total'],     
            $_POST['copies_available'], 
            $_POST['category'],
            $_POST['publisher'],
            $_POST['year'],
            $_POST['location']
        );
    }
    header('Location: ManageBooks.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book - Lumen Lore Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/librarian_dashboard.css">
  
</head>
<body>
    <div class="form-container">
        <h2>Edit Book</h2>
        <form method="POST">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($book['Title']); ?>" required>

            <label>Author</label>
            <input type="text" name="author" value="<?= htmlspecialchars($book['Author']); ?>" required>

            <div class="row">
                <div class="col">
                    <label>Total Copies</label>
                    <input type="number" name="copies_total" value="<?= htmlspecialchars($book['Copies_Total']); ?>" required>
                </div>
                <div class="col">
                    <label>Available Copies</label>
                    <input type="number" name="copies_available" value="<?= htmlspecialchars($book['Copies_Available']); ?>" required>
                </div>
            </div>

            <label>Category</label>
            <input type="text" name="category" value="<?= htmlspecialchars($book['Category']); ?>">

            <label>Publisher</label>
            <input type="text" name="publisher" value="<?= htmlspecialchars($book['Publisher']); ?>">

            <div class="row">
                <div class="col">
                    <label>Year</label>
                    <input type="number" name="year" value="<?= htmlspecialchars($book['Year']); ?>">
                </div>
                <div class="col">
                    <label>Location</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($book['Location']); ?>">
                </div>
            </div>

            <div class="button-group">
                <button type="submit" name="save" class="btn-save">💾 Save</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='ManageBooks.php'">✖ Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>