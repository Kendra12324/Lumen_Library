<?php
require_once __DIR__ . '/../../model/Book.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book = new Book();
    $book->addBookManual(
        $_POST['book_id'],
        $_POST['title'],
        $_POST['author'],
        $_POST['isbn'],
        $_POST['copies_total'],
        $_POST['copies_available'],
        $_POST['category'],
        $_POST['publisher'],
        $_POST['year'],
        $_POST['location']
    );
    header('Location: ManageBooks.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book - Lumen Lore Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>

body {
 
    background-color: #e0f2f1; 
    font-family: 'Cinzel', serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 120vh;
    margin: 0;
}

.add-book-container {
   
    background-color: #ffffff;
    padding: 2rem 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 77, 64, 0.15); 
    border: 1px solid #80cbc4;  
    width: 450px;
    text-align: center;
}

h2 {
    margin-bottom: 1.5rem;
    color: #004d40; 
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

input[type="text"],
input[type="number"] {
    padding: 0.8rem;

    border: 1px solid #58928eff; 
    background-color: #f2fcfb; 
    border-radius: 10px;
    outline: none;
    transition: 0.3s ease;
    font-family: 'Cinzel', serif;
    color: #01372eff;
}

input:focus {
    
    border-color: #015047ff; 
    background-color: #ffffff;
    box-shadow: 0 0 5px rgba(1, 108, 96, 0.2);
}


button {
    margin-top: 1rem;
    padding: 0.8rem;
    border: none;
    border-radius: 25px;
    
    background-color: #00695c; 
    color: #ffffff; 
    
    cursor: pointer;
    font-weight: bold;
    font-family: 'Cinzel', serif;
    transition: 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 105, 92, 0.2);
}

button:hover {
    
    background-color: #004d40; 
    transform: translateY(-2px); 
}

.back-btn {
    display: inline-block;
    margin-top: 1rem;
    color: #00796b; 
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    transition: 0.3s;
}

.back-btn:hover {
    color: #004d40; 
    text-decoration: underline;
}

::placeholder {
    color: #80cbc4;
    font-style: italic;
}
    </style>
</head>
<body>

    <div class="add-book-container">
        <h2>Add Book (Manual ID)</h2>
        <form method="POST">
            <input type="number" name="book_id" placeholder="Book ID" required>
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="isbn" placeholder="ISBN">
            <input type="number" name="copies_total" placeholder="Total Copies" required>
            <input type="number" name="copies_available" placeholder="Available Copies" required>
            <input type="text" name="category" placeholder="Category">
            <input type="text" name="publisher" placeholder="Publisher">
            <input type="number" name="year" placeholder="Year">
            <input type="text" name="location" placeholder="Location">
            <button type="submit">Add Book</button>
        </form>
        <a href="ManageBooks.php" class="back-btn">← Back to Manage Books</a>
    </div>

</body>
</html>

