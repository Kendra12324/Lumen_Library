<?php
require_once __DIR__ . '/../../model/Book.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $book = new Book();
    $book->archiveBook($_POST['book_id']);
    header('Location: ManageBooks.php');
    exit;
}

$bookId = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archive Book - Lumen Lore Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cinzel', serif;
            background-color: #d8f1f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .archive-container {
            background-color: #b3dfe6;
            padding: 2.5rem 3rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 430px;
            text-align: center;
        }

        h2 {
            color: #3b575e;
            margin-bottom: 1.5rem;
        }

        p {
            font-size: 16px;
            color: #3b575e;
            margin-bottom: 2rem;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        button {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn-confirm {
            background-color: #f47b7b;
            color: #fff;
        }

        .btn-confirm:hover {
            background-color: #ff9999;
            transform: scale(1.05);
        }

        .btn-cancel {
            background-color: #dfe79d;
        }

        .btn-cancel:hover {
            background-color: #e9f29d;
            transform: scale(1.05);
        }

        .back-link {
            display: block;
            margin-top: 1.5rem;
            color: #3b575e;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .icon {
            font-size: 45px;
            color: #f47b7b;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="archive-container">
    <div class="icon">⚠️</div>
    <h2>Archive Book</h2>
    <p>Are you sure you want to archive this book (ID: <strong><?= htmlspecialchars($bookId) ?></strong>)?<br>
       Once archived, it will be hidden from the main book list.</p>

    <form method="POST">
        <input type="hidden" name="book_id" value="<?= htmlspecialchars($bookId) ?>">
        <div class="btn-group">
            <button type="submit" name="confirm" class="btn-confirm">Yes, Archive</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='ManageBooks.php'">Cancel</button>
        </div>
    </form>

    <a href="ManageBooks.php" class="back-link">← Back to Manage Books</a>
</div>

</body>
</html>
