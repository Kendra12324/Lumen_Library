<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->conn;
$user_id = $_SESSION['user_id'];

// Get User Details
$sql = "SELECT * FROM user WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass && !empty($new_pass)) {
        
        $updateSql = "UPDATE user SET PasswordHash = ? WHERE User_ID = ?";
        
        $updateStmt = $conn->prepare($updateSql);
        
        $updateStmt->bind_param("si", $new_pass, $user_id);
        
        if ($updateStmt->execute()) {
            $msg = "<span style='color: #059669;'>Password updated successfully!</span>";
        } else {
            $msg = "<span style='color: #b91c1c;'>Error updating password.</span>";
        }
    } else {
        $msg = "<span style='color: #b91c1c;'>Passwords do not match.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | Lumen Lore</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../assets/css/user_dashboard.css">

 
</head>
<body>

  <div class="sidebar" id="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="borrow.php"><i class="fas fa-book-open"></i><span>Borrow</span></a>
    <a href="reservation.php"><i class="fas fa-calendar-alt"></i><span>Reservation</span></a>
    <a href="payments.php"><i class="fas fa-file-invoice-dollar"></i><span>Payments</span></a>
    <a href="Book.php"><i class="fas fa-book"></i><span>Books</span></a>
    
    <div style="margin-top: auto; width: 100%; display: flex; flex-direction: column; align-items: center; padding-bottom: 20px;">
        <a href="#" class="active"><i class="fas fa-user-circle"></i><span>My Profile</span></a>
        <a href="logout.php" style="color: #fda4af;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="collapse-btn" id="toggleBtn"><i class="fas fa-chevron-left"></i></div>

  <div class="main-content">
    <h1>Student Profile</h1>
    
    <div class="profile-container">
        
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar-circle">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h2 style="margin:0; color: #134e4a;"><?= htmlspecialchars($user['Name']) ?></h2>
                <p style="margin:5px 0 0; color: #d97706;">Student</p>
            </div>

            <div class="info-group">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?= htmlspecialchars($user['Email'] ?? 'Not set') ?></div>
            </div>

            <div class="info-group">
                <div class="info-label">User ID</div>
                <div class="info-value">Student-<?= str_pad($user['User_ID'], 4, '0', STR_PAD_LEFT) ?></div>
            </div>

            <div class="info-group">
                <div class="info-label">Account Status</div>
                <div class="info-value" style="color: #059669;"> <i class="fas fa-check-circle"></i> Active</div>
            </div>
        </div>

        <div class="profile-card">
            <h3 style="color: #134e4a; border-bottom: 2px solid #f0f4f8; padding-bottom: 10px; margin-top: 0;">
                <i class="fas fa-lock"></i> Security Settings
            </h3>
            
            <p>Change your password regularly to keep your account secure.</p>
            
            <?php if ($msg): ?>
                <div style="margin-bottom: 15px; font-weight: bold;"><?= $msg ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                </div>

                <button type="submit" name="update_password" class="save-btn">Update Password</button>
            </form>
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