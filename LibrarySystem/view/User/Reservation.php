<?php
session_start();
require_once __DIR__ . '/../../model/Reservation.php';
require_once __DIR__ . '/../../model/Book.php';

// Check if student is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$reservationModel = new Reservation();
$bookModel = new Book();

$reservations = $reservationModel->getReservationsByUser($user_id);

$books = $bookModel->getAllBooks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reservation Page</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/user_dashboard.css">

</head>
<body>

  <div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="borrow.php"><i class="fas fa-book-open"></i><span>Borrow</span></a>
    <a href="#" class="active"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
    <a href="Payments.php"><i class="fas fa-clipboard-list"></i><span>Payments</span></a>
    <a href="Book.php"><i class="fas fa-book"></i><span>Books</span></a>

    <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="../../logout.php" style="color: #ffadad;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Reservation</h1>
    <button class="reserve-btn" id="openModal">Reserve Book</button>

    <table>
      <tr>
        <th>Book Title</th>
        <th>Reservation Date</th>
        <th>Expiry Date</th>
        <th>Status</th>
      </tr>
      <?php if (empty($reservations)): ?>
      <tr><td colspan="4" style="text-align:center;">No reservations found</td></tr>
      <?php else: foreach ($reservations as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['Book_Title']) ?></td>
        <td><?= htmlspecialchars($r['Reservation_Date']) ?></td>
        <td><?= htmlspecialchars($r['Expiry_Date']) ?></td>
        <td><?= htmlspecialchars($r['Status']) ?></td>
      </tr>
      <?php endforeach; endif; ?>
    </table>
  </div>

  <div class="modal" id="reservationModal">
    <div class="modal-content">
      <h2>Reserve a Book</h2>
      <form action="../../controller/ReservationController.php" method="POST">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <label for="book">Select Book:</label><br>
        <select name="book_id" required>
          <option value="">-- Choose a book --</option>
          <?php foreach ($books as $book): ?>
            <option value="<?= $book['Book_ID'] ?>"><?= htmlspecialchars($book['Title']) ?></option>
          <?php endforeach; ?>
        </select>
        <br>

        <label for="reservation_date">Reservation Date:</label><br>
        <input type="date" name="reservation_date" value="<?= date('Y-m-d') ?>" required readonly><br>

        <label for="remarks">Remarks (optional):</label><br>
        <input type="text" name="remarks" placeholder="Any notes..." style="width:90%;"><br>

        <button type="button" class="btn-cancel" id="closeModal">Cancel</button>
        <button type="submit" class="btn-confirm">Confirm</button>
      </form>
    </div>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const icon = toggleBtn.querySelector('i');
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      icon.classList.toggle('fa-chevron-right');
      icon.classList.toggle('fa-chevron-left');
    });

    const modal = document.getElementById('reservationModal');
    const openModal = document.getElementById('openModal');
    const closeModal = document.getElementById('closeModal');
    openModal.addEventListener('click', () => modal.style.display = 'flex');
    closeModal.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => {
      if (e.target === modal) modal.style.display = 'none';
    });
  </script>
</body>
</html>
