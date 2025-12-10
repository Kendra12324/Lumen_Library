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
  <title>Borrow Page</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/user_dashboard.css">

</head>
<body>

  <div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="#" class="active"><i class="fas fa-book-open"></i><span>Borrow</span></a>
    <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
    <a href="Payments.php"><i class="fas fa-clipboard-list"></i><span>Payments</span></a>
    <a href="Book.php"><i class="fas fa-book"></i><span>Books</span></a>


    <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>

        <a href="logout.php" style="color: #ffadad;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Borrow</h1>

    <a href="borrowbookform.php" class="borrow-btn">Borrow Books</a>

    <table>
      <tr>
        <th>Title</th>
        <th>Semester</th>
        <th>Borrow Date</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Return Date</th>
      </tr>

      

      <?php if (count($records) > 0): ?>
        <?php foreach ($records as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['Book_Title']) ?></td>
            <td><?= htmlspecialchars($row['Semester_Name']) ?></td>
            <td><?= date('m/d/Y', strtotime($row['Borrow_Date'])) ?></td>
            <td><?= date('m/d/Y', strtotime($row['Due_Date'])) ?></td>
            <td><?= htmlspecialchars($row['Status']) ?></td>
            <td><?= $row['Return_Date'] ? date('m/d/Y', strtotime($row['Return_Date'])) : '—' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;">No borrowed books found.</td>
        </tr>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
  <script>
    alert("<?= htmlspecialchars($_GET['success']); ?>");
  </script>
<?php elseif (isset($_GET['error'])): ?>
  <script>
    alert("<?= htmlspecialchars($_GET['error']); ?>");
  </script>
<?php endif; ?>

    </table>
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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  <?php if (isset($_GET['success'])): ?>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: '<?= htmlspecialchars($_GET['success']); ?>'
    });
  <?php elseif (isset($_GET['error'])): ?>
    Swal.fire({
      icon: 'error',
      title: 'Oops!',
      text: '<?= htmlspecialchars($_GET['error']); ?>'
    });
  <?php endif; ?>
</script>


</body>
</html>
