<?php
session_start();
require_once __DIR__ . '/../../model/Reservation.php';
require_once __DIR__ . '/../../model/Borrow.php';
require_once __DIR__ . '/../../model/Book.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->conn;
$user_id = $_SESSION['user_id'];

$reservationModel = new Reservation();
$borrowModel = new Borrow();
$bookModel = new Book();

$sql = "SELECT Name FROM user WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();
$staffName = $userResult['Name'] ?? 'Staff';

$totalBooks = $bookModel->countBooks();
$totalBorrows = $borrowModel->countAllBorrows();
$totalReservations = $reservationModel->countAllReservations();
$totalActive = $borrowModel->countActiveBorrows();

$overdue_sql = "SELECT 
                    t.Borrow_ID, 
                    b.Title,
                    u.Name AS Borrower,
                    t.Due_Date,
                    DATEDIFF(NOW(), t.Due_Date) AS Days_Overdue
                FROM borrow t 
                JOIN book b ON t.Book_ID = b.Book_ID
                JOIN user u ON t.User_ID = u.User_ID
                WHERE 
                    (t.Status = 'Borrowed' AND t.Due_Date < CURDATE()) 
                    OR 
                    (t.Status = 'Overdue')
                ORDER BY t.Due_Date ASC 
                LIMIT 6";

$overdue_result = $conn->query($overdue_sql);
$overdue_books = [];

if ($overdue_result) {
    while ($row = $overdue_result->fetch_assoc()) {
        $overdue_books[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/staff_dashboard.css">

</head>
<body>
  <div class="sidebar" id="sidebar">
    <a href="dashboard.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="ManageBorrowReservation.php"><i class="fas fa-calendar-alt"></i><span>Borrow & Reservation</span></a>

    <a href="ManagePenalty.php"><i class="fas fa-money-bill"></i><span>Penalty & Payment</span></a>

        <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="logout.php" style="color: #ffadad;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Staff Dashboard Overview</h1>
    
    <p class="welcome-text">Welcome Back, <?= htmlspecialchars($staffName) ?></p>
    <a href="../../index.php" class="home-btn"><i class="fas fa-home"></i> Home</a>

    <div class="cards">
      <div class="card">
        <i class="fas fa-book"></i>
        <h3>Total Books</h3>
        <p><?= $totalBooks ?? 0 ?></p>
      </div>
      <div class="card">
        <i class="fas fa-handshake"></i>
        <h3>Total Borrows</h3>
        <p><?= $totalBorrows ?? 0 ?></p>
      </div>
      <div class="card">
        <i class="fas fa-calendar-check"></i>
        <h3>Reservations</h3>
        <p><?= $totalReservations ?? 0 ?></p>
      </div>
      <div class="card">
        <i class="fas fa-clock"></i>
        <h3>Active Borrows</h3>
        <p><?= $totalActive ?? 0 ?></p>
      </div>
    </div>

    <div class="widget-box" style="margin-top: 30px;">
    <div class="widget-header">
        <span><i class="fas fa-exclamation-circle" style="color: #c62828; margin-right: 10px;"></i>Overdue Books</span>
        
        <a href="ManageBorrowReservation.php?filter=overdue" style="font-size: 12px; color: #00796b; text-decoration: none;">View All</a>
    </div>

    <div class="table-responsive">
        <?php if (!empty($overdue_books)): ?>
            <table class="activity-table">
                <thead>
                    <tr>
                        <th style="color: #004d40; font-size: 12px;">Book Title</th>
                        <th style="color: #004d40; font-size: 12px;">Borrower</th>
                        <th style="color: #c62828; font-size: 12px;">Due Date</th> <th style="text-align: center; color: #c62828; font-size: 12px;">Days Late</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdue_books as $od): ?>
                        <tr>
                            <td style="font-weight: bold; color: #004d40;">
                                <?= htmlspecialchars($od['Title']); ?>
                            </td>
                            
                            <td style="color: #555;">
                                <i class="fas fa-user" style="font-size: 10px; color: #b2dfdb; margin-right: 5px;"></i>
                                <?= htmlspecialchars($od['Borrower']); ?>
                            </td>
                            
                            <td style="color: #c62828; font-weight: bold; font-size: 13px;">
                                <?= date('M d, Y', strtotime($od['Due_Date'])); ?>
                            </td>

                            <td style="text-align: center;">
                                <span class="days-late-badge">
                                    <?= $od['Days_Overdue']; ?> days
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 20px; color: #00796b;">
                <i class="fas fa-check-circle" style="font-size: 30px; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>Good job! No overdue books right now.</p>
            </div>
        <?php endif; ?>
    </div>
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
  </script>
</body>
</html>
