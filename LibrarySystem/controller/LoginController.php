<?php
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $userModel = new User();
    $userData = $userModel->login($username, $password);

    if ($userData) {
        $_SESSION['user_id'] = $userData['User_ID'];
        $_SESSION['username'] = $userData['Username'];
        $_SESSION['role'] = $userData['Role'];

        switch ($userData['Role']) {
            case 'Librarian':
                header('Location: ../view/librarian/dashboard.php');
                break;
            case 'Teacher':
                header('Location: ../view/teacher/dashboard.php');
                break;
            case 'Staff':
                header('Location: ../view/staff/dashboard.php');
                break;
            case 'Student':
                header('Location: ../view/user/dashboard.php');
                break;
            default:
                header('Location: ../index.php');
        }
        exit;
    } else {
        header('Location: ../view/login.php?error=invalid');
        exit;
    }
} else {
    header('Location: ../view/login.php');
    exit;
}
?>
