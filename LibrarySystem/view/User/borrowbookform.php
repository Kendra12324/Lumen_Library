<?php
session_start();
require_once __DIR__ . '/../../model/Borrow.php';
require_once __DIR__ . '/../../model/Book.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$bookModel = new Book();
$borrowModel = new Borrow();
$books = $bookModel->getAllBooks(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    $result = $borrowModel->borrowBook($user_id, $book_id);

    if ($result['success']) {
        header("Location: Borrow.php?success=" . urlencode($result['message']));
    } else {
        header("Location: Borrow.php?error=" . urlencode($result['message']));
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow a Book</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/user_dashboard.css">
  
</head>
<body>
    <div class="borrow-form-container">
        <h2><i class="fas fa-book"></i> Borrow a Book</h2>

        <?php if (isset($_GET['message'])): ?>
            <p class="msg"><?= htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="book_id">Select a Book</label>
            <select name="book_id" id="book_id" required>
                <option value="">-- Choose a Book --</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?= $book['Book_ID']; ?>">
                        <?= htmlspecialchars($book['Title']); ?> 
                        (<?= htmlspecialchars($book['Author']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit"><i class="fas fa-check"></i> Borrow</button>
        </form>

        <a href="Borrow.php"><i class="fas fa-arrow-left"></i> Back to Borrow List</a>
    </div>
</body>
</html>
