<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Payments | Lumen Lore</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/teacher_dashboard.css">
</head>
<body>
<body style="flex-direction: column; align-items: center; padding: 40px; overflow-y: auto;">
  <a href="dashboard.php" class="return-btn">
    <i class="fas fa-arrow-left"></i> Return to Dashboard
  </a>

  <h1><i class="fas fa-file-invoice-dollar"></i> Pending Payments</h1>

  <?php
    session_start();

    require_once __DIR__ . '/../../Model/Penalty.php';
    
    $penaltyModel = new Penalty();
    $user_id = $_SESSION['user_id'] ?? null;
    $penalties = [];
    
    if ($user_id) {
      $sql = "SELECT p.*, bk.Title AS Book_Title, b.Due_Date, DATEDIFF(CURDATE(), b.Due_Date) AS Overdue_Days, u.Name AS Borrower
              FROM Penalty p
              JOIN Borrow b ON p.Borrow_ID = b.Borrow_ID
              JOIN Book bk ON b.Book_ID = bk.Book_ID
              JOIN User u ON b.User_ID = u.User_ID
              WHERE b.User_ID = ?
              ORDER BY p.Penalty_ID DESC";
              
      $conn = $penaltyModel->getConnection();
      $stmt = $conn->prepare($sql);

      $stmt->bind_param('i', $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $penalties = $result->fetch_all(MYSQLI_ASSOC);
    }
  ?>
  
  <table>
    <thead>
      <tr>
        <th>Book Title</th>
        <th>Borrower</th>
        <th>Due Date</th>
        <th>Overdue Days</th>
        <th>Amount Due</th>
        <th>Status</th>
       
      </tr>
    </thead>
    <tbody>
    <?php if (!empty($penalties)): ?>
      <?php foreach ($penalties as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['Book_Title']) ?></td>
          <td><?= htmlspecialchars($p['Borrower']) ?></td>
          <td><?= htmlspecialchars($p['Due_Date']) ?></td>
          <td><?= max(0, (int)$p['Overdue_Days']) ?></td>
          <td style="font-weight:bold; color: #b91c1c;">₱<?= number_format($p['Amount'], 2) ?></td>
          <td><?= htmlspecialchars($p['Status']) ?></td>
          
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7" style="text-align:center; padding:30px; color:#64748b;">No pending payments found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <script>
    function payNow(button) {
      const row = button.closest("tr");

      const amount = row.cells[4].textContent; 
      const title = row.cells[0].textContent;

      if (confirm(`Confirm payment of ${amount} for "${title}"?`)) {
        row.cells[5].textContent = "Paid";
        button.textContent = "Paid";
        button.classList.add("paid");
        button.disabled = true;
        
        alert("Payment successful!");
      }
    }
  </script>

</body>
</html>