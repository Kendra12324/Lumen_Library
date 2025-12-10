<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Penalties</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/staff_dashboard.css">

</head>
<body>
  <div class="sidebar" id="sidebar">
    <a href="Dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="ManageBorrowReservation.php"><i class="fas fa-calendar-alt"></i><span>Borrow & Reservation</span></a>

    <a href="ManagePenalty.php" class="active"><i class="fas fa-money-bill"></i><span>Penalty & Payment</span></a>
            <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="profile.php"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="logout.php" style="color: #ffadad;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1><i class="fas fa-money-bill"></i> Manage Penalties</h1>
    <?php
      require_once __DIR__ . '/../../Model/Penalty.php';
      $penaltyModel = new Penalty();
      $penalties = $penaltyModel->getAllPenalties();
    ?>
    <table>
      <tr>
        <th>Borrower</th>
        <th>Book Title</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php if (!empty($penalties) && count($penalties) > 0): ?>
        <?php foreach ($penalties as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['User_Name']) ?></td>
            <td><?= htmlspecialchars($p['Book_Title']) ?></td>
            <td>₱<?= number_format($p['Amount'], 2) ?></td>
            <td><?= htmlspecialchars($p['Status']) ?></td>
            <td>
              <form action="../../controller/PenaltyController.php" method="POST" style="display:inline;">
                <input type="hidden" name="penalty_id" value="<?= $p['Penalty_ID'] ?>">
                <?php if ($p['Status'] !== 'Paid'): ?>
                  <button type="submit" name="action" value="mark_paid" class="btn btn-paid">
                    <i class="fas fa-check"></i> Mark as Paid
                  </button>
                <?php endif; ?>
                <button type="submit" name="action" value="delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this penalty?');">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No penalties found.</td></tr>
      <?php endif; ?>
    </table>
  </div>
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
    });
  </script>
</body>
</html>
