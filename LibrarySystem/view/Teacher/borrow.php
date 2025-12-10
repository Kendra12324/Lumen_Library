<?php
session_start();

require_once __DIR__ . '/../../model/Borrow.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$userID = $_SESSION['user_id'];
$borrowModel = new Borrow();

$borrowModel->updateOverdueStatus();

$conn = $borrowModel->getConnection();

$sql = "SELECT 
            b.Borrow_ID,
            COALESCE(bk.Title, 'Unknown Book') AS Book_Title,
            CASE 
                WHEN MONTH(b.Borrow_Date) >= 8 OR MONTH(b.Borrow_Date) <= 1 THEN 'First Semester'
                ELSE 'Second Semester'
            END AS Semester_Name,
            b.Borrow_Date,
            b.Due_Date,
            b.Status,
            b.Return_Date
        FROM Borrow b
        LEFT JOIN Book bk ON b.Book_ID = bk.Book_ID
        WHERE b.User_ID = ?
        ORDER BY b.Borrow_ID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userID);
$stmt->execute();
$records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Teacher Borrowing | Lumen Lore</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/teacher_dashboard.css">
</head>
<body>

  <div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="#" class="active"><i class="fas fa-book-open"></i><span>Borrow</span></a> <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
    <a href="payments.php"><i class="fas fa-file-invoice-dollar"></i><span>Payments</span></a>
    <a href="Book.php"><i class="fas fa-book"></i><span>Books</span></a>

    <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="logout.php" style="color: #ffadad;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Teacher Borrowing Record</h1>

    <a href="borrowbookform.php" class="borrow-btn"><i class="fas fa-plus"></i> Borrow Books</a>

    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Semester</th>
          <th>Borrow Date</th>
          <th>Due Date</th>
          <th>Status</th>
          <th>Return Date</th>
        </tr>
      </thead>
      <tbody>
      <?php if (count($records) > 0): ?>
        <?php foreach ($records as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['Book_Title']) ?></td>
            <td><?= htmlspecialchars($row['Semester_Name']) ?></td>
            <td><?= date('F j, Y', strtotime($row['Borrow_Date'])) ?></td>
            <td><?= date('F j, Y', strtotime($row['Due_Date'])) ?></td>
            
            <td style="font-weight:bold; color: <?= ($row['Status'] == 'Returned') ? '#059669' : (($row['Status'] == 'Overdue') ? '#b91c1c' : '#d97706') ?>">
                <?= htmlspecialchars($row['Status']) ?>
            </td>
            
            <td><?= $row['Return_Date'] ? date('F j, Y', strtotime($row['Return_Date'])) : '—' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center; padding: 30px; color: #64748b;">No borrowed books found.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
    
    <?php if (isset($_GET['success'])): ?>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: '<?= htmlspecialchars($_GET['success']); ?>',
          confirmButtonColor: '#0f766e'
        });
      </script>
    <?php elseif (isset($_GET['error'])): ?>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Oops!',
          text: '<?= htmlspecialchars($_GET['error']); ?>',
          confirmButtonColor: '#b91c1c'
        });
      </script>
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