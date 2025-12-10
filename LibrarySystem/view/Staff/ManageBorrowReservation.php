<?php
require_once __DIR__ . '/../../model/Borrow.php';
require_once __DIR__ . '/../../model/Reservation.php';

$borrowModel = new Borrow();
$reservationModel = new Reservation();

$borrowModel->updateOverdueStatus();

$borrows = $borrowModel->getAllBorrowRecords();
$reservations = $reservationModel->getAllReservations();

$success = isset($_GET['success']) ? htmlspecialchars(urldecode($_GET['success'])) : null;
$error = isset($_GET['error']) ? htmlspecialchars(urldecode($_GET['error'])) : null;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
   
    $reservations = array_filter($reservations, function($row) use ($search) {
      
        return stripos($row['User_Name'], $search) !== false || 
               stripos($row['Book_Title'], $search) !== false;
    });

    $borrows = array_filter($borrows, function($row) use ($search) {
        return stripos($row['User_Name'], $search) !== false || 
               stripos($row['Book_Title'], $search) !== false;
    });
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Borrow & Reservation Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/staff_dashboard.css">
</head>

<body>
  <div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <!-- Removed Manage Books -->
    <a href="ManageBorrowReservation.php" class="active"><i class="fas fa-calendar-alt"></i><span>Borrow & Reservation</span></a>
    <a href="ManagePenalty.php"><i class="fas fa-money-bill"></i><span>Penalty & Payment</span></a>
        <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php" class="active"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="logout.php" style="color: #fda4af;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  
  <div class="main-content">
    <h1>Borrow & Reservation Management</h1>

    <div class="search-container" style="margin-bottom: 20px;">
    <form action="" method="GET">
        <input type="text" name="search" 
               value="<?= htmlspecialchars($search) ?>" 
               placeholder="Search by User or Book..."
               style="padding: 8px; width: 300px;">
        
        <button type="submit" class="btn" style="background-color: #007bff; color: white;">
            <i class="fas fa-search"></i> Search
        </button>

        <?php if($search): ?>
            <a href="ManageBorrowReservation.php" class="btn" style="background-color: #6c757d; color: white; text-decoration: none; padding: 9px 12px; display:inline-block;">
                Clear
            </a>
        <?php endif; ?>
    </form>
</div>

    <!-- Flash messages -->
    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>


    <h2>📘 Currently Borrowed Books</h2>
    <table>
      <tr>
        <th>Borrow ID</th><th>User</th><th>Book</th><th>Borrow Date</th><th>Due Date</th><th>Status</th><th>Action</th>
      </tr>
      <?php if (!empty($borrows)): ?>
        <?php foreach ($borrows as $row): ?>
         
          <tr>
            <td><?= $row['Borrow_ID']; ?></td>
            <td><?= $row['User_Name']; ?></td>
            <td><?= $row['Book_Title']; ?></td>
            <td><?= $row['Borrow_Date']; ?></td>
            <td><?= $row['Due_Date']; ?></td>
            <td><?= $row['Status']; ?></td>
            <td>
              <form action="../../controller/BorrowController.php" method="POST" style="display:inline;">
                <input type="hidden" name="borrow_id" value="<?= $row['Borrow_ID']; ?>">
                <button class="btn return" name="action" value="markReturned">Mark Returned</button>
              </form>
              <form action="../../controller/BorrowController.php" method="POST" style="display:inline;">
                <input type="hidden" name="borrow_id" value="<?= $row['Borrow_ID']; ?>">
                <button class="btn renew" name="action" value="renew">Renew</button>
              </form>
              <form action="../../controller/BorrowController.php" method="POST" style="display:inline;">
                <input type="hidden" name="borrow_id" value="<?= $row['Borrow_ID']; ?>">
                <button class="btn overdue" name="action" value="markOverdue">Overdue</button>
              </form>
              <form action="../../controller/BorrowController.php" method="POST" style="display:inline;">
                <input type="hidden" name="borrow_id" value="<?= $row['Borrow_ID']; ?>">
                <button class="btn lost" name="action" value="markLost">Lost</button>
              </form>
            </td>
          </tr>
          
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">No borrow records found.</td></tr>
      <?php endif; ?>
    </table>


    <h2>📗 Book Reservations</h2>
    <table>
      <tr>
        <th>Reservation ID</th>
        <th>User</th>
        <th>Book</th>
        <th>Reservation Date</th>
        <th>Expiry Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php if (!empty($reservations)): ?>
        <?php foreach ($reservations as $row): ?>
          <tr>
            <td><?= $row['Reservation_ID']; ?></td>
            <td><?= $row['User_Name']; ?></td>
            <td><?= $row['Book_Title']; ?></td>
            <td><?= $row['Reservation_Date']; ?></td>
            <td><?= $row['Expiry_Date']; ?></td>
            <td><?= $row['Status']; ?></td>
            <td>
              <form action="../../controller/ReservationController.php" method="POST" style="display:inline;">
                <input type="hidden" name="reservation_id" value="<?= $row['Reservation_ID']; ?>">
                <button class="btn cancel" name="action" value="cancel">Cancel</button>
              </form>

              <form action="../../controller/ReservationController.php" method="POST" style="display:inline;">
    <input type="hidden" name="reservation_id" value="<?= $row['Reservation_ID']; ?>">
    <button class="btn renew" name="action" value="approve">Approve</button>
</form>      

<form action="../../controller/ReservationController.php" method="POST" style="display:inline;">
    <input type="hidden" name="reservation_id" value="<?= $row['Reservation_ID']; ?>">
    <button class="btn penalty" name="action" value="delete">Delete</button>
</form>



            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">No reservation records found.</td></tr>
      <?php endif; ?>
    </table>
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


  </script>
</body>
</html>
