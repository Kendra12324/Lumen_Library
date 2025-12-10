<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../model/Borrow.php';
require_once __DIR__ . '/../../model/Reservation.php';
require_once __DIR__ . '/../../model/Penalty.php'; 

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->conn;
$user_id = $_SESSION['user_id'];

$borrowModel = new Borrow($conn);
$reservationModel = new Reservation($conn); 
$penaltyModel = new Penalty($conn); 

// 1. Get Student Name
$sql = "SELECT Name FROM user WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();
$studentName = $userResult['Name'] ?? 'Student';

$borrowCount = $borrowModel->getActiveBorrowCount($user_id);

$reserveCount = $reservationModel->countActiveReservationsByUser($user_id);


$fines = $penaltyModel->getTotalUnpaidFines($user_id);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="stylesheet" href="../../assets/css/user_dashboard.css">
</head>
<body>

  <div class="sidebar" id="sidebar">

    <a href="#" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="borrow.php"><i class="fas fa-book-open"></i><span>Borrow</span></a>
    <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
    <a href="payments.php"><i class="fas fa-file-invoice-dollar"></i><span>Payments</span></a>
    <a href="Book.php"><i class="fas fa-book"></i><span>Books</span></a>

    <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="Logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Student Dashboard</h1>
    
    <p class="welcome-text">Welcome Back, <?= htmlspecialchars($studentName) ?></p>
    <a href="../../index.php" class="home-btn"><i class="fas fa-home"></i> Home</a>

    <div class="search-bar">
      <input type="text" placeholder="Search...">
      <i class="fas fa-search"></i>
    </div>

    <div class="card-container">
      <div class="card">
        <i class="fas fa-book"></i>
        <p>Borrowed</p>
        <span><?= $borrowCount ?></span>
      </div>
      <div class="card">
        <i class="fas fa-calendar-check"></i>
        <p>Reserved</p>
        <span><?= $reserveCount ?></span>
      </div>
      <div class="card">
        <i class="fas fa-gavel"></i>
        <p>Unpaid Fines</p>
        <span style="color: <?= $fines > 0 ? '#b22222' : '#2d3a22' ?>">
            ₱<?= number_format($fines, 2) ?>
        </span>
      </div>
    </div>

    <div class="shelf-section">
        <div class="shelf-title">
            <i class="fas fa-gem" style="color: #d97706;"></i> 
            New Arrivals & Collections
        </div>
        
        <div class="bookshelf">
            <a href="Book.php" class="book-spine book-1">
                <i class="fas fa-book"></i>
                <div class="tooltip">Explore Fiction</div>
            </a>

            <a href="Book.php" class="book-spine book-2">
                <i class="fas fa-atlas"></i>
                <div class="tooltip">History Archives</div>
            </a>

            <a href="Book.php" class="book-spine book-3">
                <i class="fas fa-flask"></i>
                <div class="tooltip">Science Dept</div>
            </a>

            <a href="Book.php" class="book-spine book-lean">
                <i class="fas fa-feather-alt"></i>
                <div class="tooltip">Poetry</div>
            </a>

            <div style="width: 20px;"></div>

            <a href="borrow.php" class="book-spine book-4">
                <i class="fas fa-history"></i>
                <div class="tooltip">Your History</div>
            </a>

            <a href="payments.php" class="book-spine book-6">
                <i class="fas fa-coins"></i>
                <div class="tooltip">Payments</div>
            </a>
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
  </script>
</body>
</html>