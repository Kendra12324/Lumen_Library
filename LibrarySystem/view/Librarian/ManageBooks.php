<?php 
session_start();
require_once __DIR__ . '/../../model/Book.php';
$bookModel = new Book();

$books = $bookModel->getActiveBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Books - Lumen Lore Library</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/librarian_dashboard.css">

</head>
<body>

  <div class="sidebar" id="sidebar">
    <button onclick="window.location.href='dashboard.php'">
      <i class="fas fa-home"></i><span>Dashboard</span>
    </button>
    
    <button class="active" onclick="window.location.href='ManageBooks.php'">
      <i class="fas fa-book"></i><span>Manage Books</span>
    </button>
    
    <button onclick="window.location.href='ViewArchived.php'">
      <i class="fas fa-box-archive"></i><span>Archived Books</span>
    </button>

    <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        
        <button onclick="window.location.href='profile.php'">
            <i class="fas fa-user-circle"></i><span>My Profile</span>
        </button>

        <button onclick="window.location.href='logout.php'" style="color: #fda4af;"> <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </button>

    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="content" id="content">
    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
      <h2>Manage Books</h2>
      <button class="add-book-btn" onclick="window.location.href='AddBook.php'">Add Book</button>
    </div>

    <?php if (!empty($books)): ?>
      <table>
  <tr>
    <th>Book ID</th>
    <th>Title</th>
    <th>Author</th>
    <th>Copies Total</th> 
    <th>Copies Available</th>
    <th>Location</th>
    <th>Action</th>

  </tr>
  <?php foreach ($books as $book): ?>
    <tr>
      <td><?= htmlspecialchars($book['Book_ID']); ?></td>
      <td><?= htmlspecialchars($book['Title']); ?></td>
      <td><?= htmlspecialchars($book['Author']); ?></td>
      <td><?= htmlspecialchars($book['Copies_Total']); ?></td> <!-- ✅ Added -->
      <td><?= htmlspecialchars($book['Copies_Available']); ?></td>
      <td><?= htmlspecialchars($book['Location']); ?></td>

      <td>
        <button class="btn-edit" onclick="window.location.href='EditBook.php?id=<?= $book['Book_ID']; ?>'">Edit</button>
        <button class="btn-archive" onclick="window.location.href='ArchiveBook.php?id=<?= $book['Book_ID']; ?>'">Archive</button>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

    <?php else: ?>
      <p class="no-data">No available books to display.</p>
    <?php endif; ?>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const icon = toggleBtn.querySelector('i');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      icon.classList.toggle('fa-chevron-left');
      icon.classList.toggle('fa-chevron-right');
    });
  </script>

</body>
</html>
