<?php
session_start();

require_once __DIR__ . '/../../model/Book.php';
require_once __DIR__ . '/../../model/Borrow.php'; 
require_once __DIR__ . '/../../config/Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
} 

$bookModel = new Book();
$borrowModel = new Borrow(); 
$db = new Database();
$conn = $db->conn;

$user_id = $_SESSION['user_id'];

$sql = "SELECT Name FROM user WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();
$staffName = $userResult['Name'] ?? 'Librarian';


$allBooks = $bookModel->getAllBooks();
$totalBooks = count($allBooks);
$availableBooks = 0;
$archivedBooks = 0;

foreach ($allBooks as $book) {
    
    $status = strtolower(trim($book['Status'] ?? ''));
    $copiesAvailable = (int)($book['Copies_Available'] ?? 0);

    if ($status === 'archived') {
        $archivedBooks++;
    } 

    elseif ($status === 'available' || $copiesAvailable > 0) {
        $availableBooks++;
    }
}

$borrowedBooks = $borrowModel->countActiveBorrows(); 

$recentSql = "SELECT u.Name, b.Title, br.Status, br.Borrow_Date 
              FROM Borrow br
              JOIN User u ON br.User_ID = u.User_ID
              JOIN Book b ON br.Book_ID = b.Book_ID
              ORDER BY br.Borrow_Date DESC LIMIT 5";
$recentStmt = $conn->prepare($recentSql);
$recentStmt->execute();
$recentActivities = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$overdueSql = "SELECT u.Name, b.Title, br.Due_Date 
               FROM Borrow br
               JOIN User u ON br.User_ID = u.User_ID
               JOIN Book b ON br.Book_ID = b.Book_ID
               WHERE br.Status = 'Overdue' OR (br.Status = 'Borrowed' AND br.Due_Date < CURDATE())
               LIMIT 5";
$overdueStmt = $conn->prepare($overdueSql);
$overdueStmt->execute();
$overdueList = $overdueStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?> 


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Librarian Dashboard - Lumen Lore Library</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/librarian_dashboard.css">

</head>
<body> 

  <div class="sidebar" id="sidebar">
    <button class="active" onclick="window.location.href='dashboard.php'">
      <i class="fas fa-home"></i><span>Dashboard</span>
    </button>
    <button onclick="window.location.href='ManageBooks.php'">
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

  <div class="main-content" id="content">
    <h1>Welcome, Librarian </h1>
       <p class="welcome-text">Welcome Back, <?= htmlspecialchars($staffName) ?></p>
       <a href="../../index.php" class="home-btn"><i class="fas fa-home"></i> Home</a>

    <div class="card-container">
      <div class="card">
        <i class="fas fa-book"></i>
        <p>Total Books: <?= $totalBooks; ?></p>
      </div>
      <div class="card">
        <i class="fas fa-book-open"></i>
        <p>Available: <?= $availableBooks; ?></p>
      </div>
      <div class="card">
        <i class="fas fa-hand-holding"></i>
        <p>Borrowed: <?= $borrowedBooks; ?></p>
      </div>
      <div class="card">
        <i class="fas fa-archive"></i>
        <p>Archived: <?= $archivedBooks; ?></p>
      </div>
      
    </div>
     <div class="dashboard-grid">
        
        <div class="widget-box">
            <div class="widget-header">
                <span><i class="fas fa-clock"></i> Recent Activity</span>
            </div>
            <table class="activity-table">
                <?php if(!empty($recentActivities)): ?>
                    <?php foreach($recentActivities as $act): 
                      
                        $statusClass = 'status-borrowed';
                        if($act['Status'] == 'Returned') $statusClass = 'status-returned';
                        if($act['Status'] == 'Overdue') $statusClass = 'status-overdue';
                    ?>
                    <tr>
                        <td width="35%"><strong><?= htmlspecialchars($act['Name']) ?></strong></td>
                        <td width="45%"><?= htmlspecialchars($act['Title']) ?></td>
                        <td><span class="status-badge <?= $statusClass ?>"><?= $act['Status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" align="center">No recent activity.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="widget-box">
            <div class="widget-header">
                <span style="color:#c62828;"><i class="fas fa-exclamation-circle"></i> Overdue Alerts</span>
            </div>
            
            <?php if(!empty($overdueList)): ?>
                <?php foreach($overdueList as $od): ?>
                <div class="overdue-item">
                    <div class="date-box">
                        <span><?= date('d', strtotime($od['Due_Date'])) ?></span>
                        <small><?= date('M', strtotime($od['Due_Date'])) ?></small>
                    </div>
                    <div class="overdue-info">
                        <h4><?= htmlspecialchars($od['Name']) ?></h4>
                        <p>Book: <em><?= htmlspecialchars($od['Title']) ?></em></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#666; font-size:14px; margin-top:20px;">
                    <i class="fas fa-check-circle" style="color:#2e7d32; font-size:24px;"></i><br>
                    No overdue books!
                </p>
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
      icon.classList.toggle('fa-chevron-left');
      icon.classList.toggle('fa-chevron-right');
    });
  </script>
</body>
</html>
