<?php
session_start();
require_once __DIR__ . '/../../model/Book.php';

$bookModel = new Book();
$archivedBooks = $bookModel->getArchivedBooks(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Books - Lumen Lore Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/librarian_dashboard.css">
</head>
<body>
   
    <div class="sidebar" id="sidebar">
        <button onclick="window.location.href='dashboard.php'">
            <i class="fas fa-home"></i><span>Dashboard</span>
        </button>
        <button onclick="window.location.href='ManageBooks.php'">
            <i class="fas fa-book"></i><span>Manage Books</span>
        </button>
        <button class="active">
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

   
    <div class="main-content" id="mainContent">
        <h2>Archived Books</h2>

        <?php if (!empty($archivedBooks)): ?>
        <table>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Publisher</th>
                <th>Action</th>
            </tr>
            <?php foreach ($archivedBooks as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['Book_ID']); ?></td>
                    <td><?= htmlspecialchars($book['Title']); ?></td>
                    <td><?= htmlspecialchars($book['Author']); ?></td>
                    <td><?= htmlspecialchars($book['Category']); ?></td>
                    <td><?= htmlspecialchars($book['Publisher']); ?></td>
                    <td>
                        <button class="btn-restore" onclick="window.location.href='RestoreBook.php?id=<?= $book['Book_ID']; ?>'">
                            Restore
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p class="no-data">No archived books found.</p>
        <?php endif; ?>
    </div>

    <script>
      const sidebar = document.getElementById('sidebar');
      const toggleBtn = document.getElementById('toggleBtn');
      const icon = toggleBtn.querySelector('i');

      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
          icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
        } else {
          icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
        }
      });
    </script>
</body>
</html>
