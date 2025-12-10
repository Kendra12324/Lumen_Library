<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Lumen Lore Library</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Cinzel', serif;
            background: url('../assets/bg-library.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .login-form-container {
            background-color: rgba(196, 238, 244, 0.9);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid #88b8cc;
        }

        .login-form-container h2 {
            font-size: 2rem;
            color: #2b4a57;
            margin-bottom: 2rem;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-wrapper input {
            width: 80%;
            padding: 1rem 1rem 1rem 2.8rem;
            border-radius: 50px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-size: 1rem;
            transition: 0.2s ease;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #7bbbd1;
            box-shadow: 0 0 0 3px rgba(135, 206, 235, 0.3);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.1rem;
        }

        .btn-form-login {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 50px;
            background-color: rgba(135, 206, 235, 1);
            color: #1d2f36;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-form-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .error-msg {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-form-container">
        <h2>Login</h2>

        <form action="../controller/LoginController.php" method="POST">
            <div class="input-wrapper">
                <i class="input-icon fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-wrapper">
                <i class="input-icon fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-form-login">Login</button>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
                <p class="error-msg">Invalid username or password.</p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
