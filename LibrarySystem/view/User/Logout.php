<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: ../login.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Logout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/user_dashboard.css">
    <style>

.logout-body {
    background-color: #f3f4f6; 
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: 'Cinzel', serif;
}

.logout-card {
    background: #ffffff;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 400px;
    width: 90%;
    border-top: 6px solid #f59e0b; 
}

.logout-icon {
    font-size: 50px;
    color: #1e3a8a;
    background: #eff6ff; 
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px auto;
    border: 2px solid #3b82f6;
}

.logout-card h2 {
    color: #1e3a8a; 
    margin-bottom: 10px;
    font-size: 24px;
    font-weight: 700;
}

.logout-card p {
    color: #6b7280; 
    margin-bottom: 30px;
    font-family: sans-serif;
    font-size: 15px;
}

.logout-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-stay {
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    background-color: #f3f4f6;
    color: #374151;
    transition: 0.3s;
    flex: 1;
    border: 1px solid #e5e7eb;
}

.btn-stay:hover {
    background-color: #e5e7eb;
    color: #1f2937;
}

.btn-logout {
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    background-color: #ef4444;
    color: white;
    transition: 0.3s;
    border: none;
    cursor: pointer;
    flex: 1;
    box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);
}

.btn-logout:hover {
    background-color: #dc2626;
    transform: translateY(-2px);
}
    </style>
</head>
<body class="logout-body">

    <div class="logout-card">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        
        <h2>Signing Out?</h2>
        <p>Are you sure you want to end your session?</p>

        <form method="POST" class="logout-actions">
            <a href="dashboard.php" class="btn-stay">Nah, Stay</a>

            <button type="submit" name="confirm_logout" class="btn-logout">Yes, Logout</button>
        </form>
    </div>

</body>
</html>