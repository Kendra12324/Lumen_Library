<?php
session_start();
require_once __DIR__ . '/../../model/Book.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$bookModel = new Book();
$books = $bookModel->getAllBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Teacher Book Catalog | Lumen Lore</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/teacher_dashboard.css">
  
</head>
<body>

  <div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="borrow.php"><i class="fas fa-book-open"></i><span>Borrow</span></a>
    <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
    <a href="payments.php"><i class="fas fa-file-invoice-dollar"></i><span>Payments</span></a>
    <a href="#" class="active"><i class="fas fa-book"></i><span>Books</span></a> <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="logout.php" style="color: #ffadad;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Available Books</h1>

    <div class="top-controls">
      <div class="btn-group">
        <button class="btn filter-btn" id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
        <button class="btn reset-btn" id="resetBtn"><i class="fas fa-undo"></i> Reset</button>
      </div>

      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search title or author...">
        <i class="fas fa-search"></i>
      </div>
    </div>

    <table id="bookTable">
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Publisher</th>
          <th>Category</th>
          <th>Copies</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($books)): ?>
        <?php foreach ($books as $book): ?>
          <tr>
            <td><?= htmlspecialchars($book['Title']); ?></td>
            <td><?= htmlspecialchars($book['Author']); ?></td>
            <td><?= htmlspecialchars($book['Publisher']); ?></td>
            <td><?= htmlspecialchars($book['Category']); ?></td>
            <td style="font-weight:bold; color: #0f766e;"><?= htmlspecialchars($book['Copies_Available']); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" style="text-align:center; padding:30px; color:#64748b;">No books available in the library.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div id="filterPopup" class="filter-popup">
    <div class="popup-content">
      <h3>Filter by Genre</h3>
      <select id="genreSelect">
        <option value="">-- Select Genre --</option>
        <option value="Fiction">Fiction</option>
        <option value="Mystery">Mystery</option>
        <option value="Romance">Romance</option>
        <option value="Science Fiction">Science Fiction</option>
        <option value="Non-fiction">Non-fiction</option>
        <option value="Biography">Biography</option>
        <option value="Fantasy">Fantasy</option>
        <option value="History">History</option>
      </select>
      <div class="popup-buttons">
        <button id="confirmFilter" class="btn filter-btn">Confirm</button>
        <button id="cancelFilter" class="btn reset-btn">Cancel</button>
      </div>
    </div>
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
  
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("bookTable");
    const rows = table.getElementsByTagName("tr");

    searchInput.addEventListener("keyup", function() {
      const filter = searchInput.value.toLowerCase();
      for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      }
    });


    const filterPopup = document.getElementById("filterPopup");
    const filterBtn = document.getElementById("filterBtn");
    const cancelBtn = document.getElementById("cancelFilter");
    const confirmBtn = document.getElementById("confirmFilter");
    const genreSelect = document.getElementById("genreSelect");

    filterBtn.addEventListener("click", () => {
      filterPopup.style.display = "flex";
    });

    cancelBtn.addEventListener("click", () => {
      filterPopup.style.display = "none";
    });

    confirmBtn.addEventListener("click", () => {
      const selectedGenre = genreSelect.value.toLowerCase();
      for (let i = 1; i < rows.length; i++) {
        const row = rows[i];

        const category = row.cells[3].textContent.toLowerCase();
        row.style.display = selectedGenre === "" || category.includes(selectedGenre) ? "" : "none";
      }
      filterPopup.style.display = "none";
    });


    document.getElementById("resetBtn").addEventListener("click", () => {
      searchInput.value = "";
      genreSelect.value = "";
      for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = "";
      }
    });
  </script>
</body>
</html>