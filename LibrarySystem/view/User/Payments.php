<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Cinzel', serif;
    }

    body {
      background-color: #b7d8dc;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px;
    }

    h1 {
      color: #2d3a22;
      font-size: 28px;
      margin-bottom: 25px;
    }

    .return-btn {
      background-color: #62713e;
      color: white;
      text-decoration: none;
      border-radius: 25px;
      padding: 10px 20px;
      margin-bottom: 25px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: 0.3s ease;
    }

    .return-btn i {
      font-size: 16px;
    }

    .return-btn:hover {
      background-color: #4a5b32;
      transform: scale(1.03);
    }

    table {
      width: 90%;
      border-collapse: collapse;
      background-color: #f2f5cd;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    th, td {
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid #c7d19e;
    }

    th {
      background-color: #62713e;
      color: white;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: #f7f9dc;
    }

    tr:hover {
      background-color: #e5edb3;
      transition: 0.2s;
    }

    .pay-btn {
      background-color: #4a707a;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 15px;
      transition: 0.3s ease;
    }

    .pay-btn:hover {
      background-color: #38575d;
    }

    .paid {
      background-color: #9dbc88;
      cursor: default;
    }

    @media (max-width: 768px) {
      table {
        width: 100%;
        font-size: 14px;
      }
    }
    .billboard {
      background-color: #fff;
      border-left: 8px solid #d97706;
      width: 90%;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 30px;
    }

    .billboard-icon {
      background-color: #fff7e6;
      color: #d97706;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      flex-shrink: 0;
    }

    .billboard-content h3 {
      margin: 0 0 5px 0;
      color: #2d3a22; 
      font-size: 18px;
      font-weight: 700;
    }

    .billboard-content p {
      margin: 0;
      color: #555;
      font-size: 14px;
      line-height: 1.5;
      font-family: sans-serif; 
    }

    @media (max-width: 768px) {
      .billboard {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <a href="dashboard.php" class="return-btn">
    <i class="fas fa-arrow-left"></i> Return to Dashboard
  </a>

  <h1>Pending Payments</h1>

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
    <div class="billboard">
      <div class="billboard-icon">
          <i class="fas fa-cash-register"></i>
      </div>
      <div class="billboard-content">
          <h3>Payment Instruction</h3>
          <p>
              Please proceed to the <strong>Library Counter</strong> to settle your pending fines. 
              Show your Student ID to the Staff. Once payment is made, your records will be updated automatically.
              <br><br>
              <em style="color: #d97706;">Note: Online payments are currently unavailable.</em>
          </p>
      </div>
  </div>

  <table>
    <tr>
      <th>Book Title</th>
      <th>Borrower</th>
      <th>Due Date</th>
      <th>Overdue Days</th>
      <th>Amount Due (₱)</th>
      <th>Status</th>
    
    </tr>
    <?php if (!empty($penalties)): ?>
      <?php foreach ($penalties as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['Book_Title']) ?></td>
          <td><?= htmlspecialchars($p['Borrower']) ?></td>
          <td><?= htmlspecialchars($p['Due_Date']) ?></td>
          <td><?= max(0, (int)$p['Overdue_Days']) ?></td>
          <td><?= number_format($p['Amount'], 2) ?></td>
          <td><?= htmlspecialchars($p['Status']) ?></td>
          
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7" style="text-align:center;">No payments found.</td></tr>
    <?php endif; ?>
  </table>

  <script>
    function payNow(button) {
      const row = button.closest("tr");
      const amount = row.cells[4].textContent;
      const title = row.cells[0].textContent;

      if (confirm(`Confirm payment of ₱${amount} for "${title}"?`)) {
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
