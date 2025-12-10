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
    <link rel="stylesheet" href="../../assets/css/staff_dashboard.css">
    <style>
      
.logout-body {
    background-color: #e0f2f1; 
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: 'Cinzel', serif;
    color: #004d40;
}

.logout-card {
    background: #ffffff;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 77, 64, 0.15);
    border: 1px solid #80cbc4;
    border-top: 6px solid #00695c; 
    
    text-align: center;
    max-width: 400px;
    width: 90%;
}

.logout-icon {
    font-size: 50px;
    color: #c62828;
    background: #ffebee; 
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px auto;
    border: 2px solid #ef9a9a;
}

.logout-card h2 {
    color: #004d40; 
    margin-bottom: 10px;
    font-size: 24px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.logout-card p {
    color: #00796b; 
    margin-bottom: 30px;
    font-family: sans-serif; 
    font-size: 15px;
    line-height: 1.5;
}

.logout-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-stay {
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    background-color: #e0f2f1;
    color: #00695c;
    border: 1px solid #b2dfdb;
    
    transition: 0.3s;
    flex: 1;
    font-family: 'Cinzel', serif;
}

.btn-stay:hover {
    background-color: #b2dfdb;
    color: #004d40;
    border-color: #00695c;
}

.btn-logout {
    padding: 12px 24px;
    border-radius: 25px; 
    text-decoration: none;
    font-weight: 600;
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ef9a9a;
    transition: 0.3s;
    cursor: pointer;
    flex: 1;
    font-family: 'Cinzel', serif;
    box-shadow: none; 
}

.btn-logout:hover {
    background-color: #c62828;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(198, 40, 40, 0.2);
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
            <a href="dashboard.php" class="btn-stay">Stay</a>

            <button type="submit" name="confirm_logout" class="btn-logout">Yes, Logout</button>
        </form>
    </div>

</body>
</html>