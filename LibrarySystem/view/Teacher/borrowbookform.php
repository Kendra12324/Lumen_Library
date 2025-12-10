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

        $result = $borrowModel->borrowBookUnlimited($user_id, $book_id);

    if ($result['success']) {
        header("Location: borrow.php?success=" . urlencode($result['message']));
    } else {
        header("Location: borrow.php?error=" . urlencode($result['message']));
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Borrow Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    
        body {
            font-family: 'Cinzel', serif;
            background-color: #e6dfcc; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .borrow-form-container {

            background-color: #fdfbf7;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            text-align: center;

            border: 2px solid #9c7c38;
        }

        h2 {

            color: #5c4b26;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        select {
            padding: 0.8rem 1rem;
            border-radius: 25px;
            border: 1px solid #9c7c38;
            font-size: 1rem;
            background-color: #fff;
            outline: none;
            color: #333;
        }

        select:focus {
            border-color: #5c4b26;
            box-shadow: 0 0 0 3px rgba(156, 124, 56, 0.2);
        }

        button {
            padding: 0.9rem;
            border: none;
            border-radius: 25px;
            background-color: #9c7c38;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.2s ease;
        }

        button:hover {
            background-color: #705826;
        }

        a {
            display: inline-block;
            margin-top: 1rem;
            color: #5c4b26;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .msg {
            margin-bottom: 1rem;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: bold;
        }

        .msg.success {
            background-color: #dcedc8;
            color: #33691e;
        }

        .msg.error {
            background-color: #ffccbc;
            color: #bf360c;
        }
    </style>
</head>
<body>
    <div class="borrow-form-container">
        <h2><i class="fas fa-chalkboard-teacher"></i> Teacher Borrow Form</h2>

        <?php if (isset($_GET['message'])): ?>
            <p class="msg"><?= htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="book_id" style="text-align:left; font-weight:bold; color:#5c4b26;">Select a Book</label>
            <select name="book_id" id="book_id" required>
                <option value="">-- Choose a Book --</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?= $book['Book_ID']; ?>">
                        <?= htmlspecialchars($book['Title']); ?> 
                        (<?= htmlspecialchars($book['Author']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit"><i class="fas fa-check"></i> Confirm Borrow</button>
        </form>

        <a href="borrow.php"><i class="fas fa-arrow-left"></i> Back to Borrow List</a>
    </div>
</body>
</html>