<?php
session_start();
require_once __DIR__ . '/../model/User.php';

$userModel = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif ($userModel->usernameExists($username)) {
        $error = "Username is already taken.";
    } else {
        if ($userModel->register($name, $username, $password, $role)) {
            
            $_SESSION['role'] = $role;
            
            if ($role === 'Student') {
                echo "<script>alert('Account Created! Redirecting to Student Dashboard...'); window.location.href='../view/User/dashboard.php';</script>";
            } else if ($role === 'Teacher') {
                echo "<script>alert('Account Created! Redirecting to Teacher Dashboard...'); window.location.href='../view/Teacher/dashboard.php';</script>";
            }
            exit;

        } else {
            $error = "Registration failed. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Lumen Lore</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
    
        body {
            margin: 0;
            padding: 0;
            font-family: 'Cinzel', serif;
            background: url('../assets/bg-library.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        /* Form Container */
        .register-card {
            background-color: rgba(196, 238, 244, 0.95); 
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px; 
            text-align: center;
            border: 1px solid #88b8cc;
        }

        .brand-title {
            font-size: 2rem;
            color: #2b4a57;
            margin-bottom: 0.5rem;
            font-weight: 800;
        }

        .subtitle {
            font-size: 1rem;
            color: #5a7d8c;
            margin-bottom: 2rem;
            font-family: sans-serif;
        }
        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #2b4a57;
            margin-left: 15px;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 0.8rem 1.2rem;
            border-radius: 50px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-size: 1rem;
            transition: 0.2s ease;
            font-family: sans-serif;
            box-sizing: border-box; 
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #7bbbd1;
            box-shadow: 0 0 0 3px rgba(135, 206, 235, 0.3);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 50px;
            background-color: rgba(135, 206, 235, 1);
            color: #1d2f36;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-family: 'Cinzel', serif;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .message {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            font-family: sans-serif;
        }
        .error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .success { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }

        .login-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            font-family: sans-serif;
            color: #5a7d8c;
        }
        .login-link a {
            color: #2b4a57;
            text-decoration: none;
            font-weight: 700;
        }
        .login-link a:hover { text-decoration: underline; }

       
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 38px; 
            color: #888;
            cursor: pointer;
        }
   
    </style>
</head>
<body>

    <div class="register-card">
        <div class="brand-title"><i class="fas fa-book-open"></i> Lumen Lore</div>
        <p class="subtitle">Create your library account</p>

        <?php if($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Juan Dela Cruz" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="juan123" required>
            </div>

            <div class="form-group">
                <label>Account Type</label>
                <select name="role" required>
                    <option value="Student">Student</option>
                    <option value="Teacher">Teacher</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
            </div>

            <button type="submit" class="btn-submit">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Log In</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>