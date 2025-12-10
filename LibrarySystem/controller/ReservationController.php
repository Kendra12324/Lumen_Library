<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/Reservation.php';

$reservationModel = new Reservation();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $reservation_id = $_POST['reservation_id'] ?? null;

    switch ($action) {
        case 'cancel':
            if ($reservationModel->cancelReservation($reservation_id)) {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?success=Reservation+Cancelled");
            } else {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?error=Failed+to+Cancel");
            }
            exit;
        break;

        case 'approve':
            if ($reservationModel->approveReservation($reservation_id)) {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?success=Reservation+Approved");
            } else {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?error=Failed+to+Approve");
            }
            exit;
        break;

        case 'fulfill':
            $borrow_id = $_POST['borrow_id'] ?? null;
            if ($reservationModel->fulfillReservation($reservation_id, $borrow_id)) {
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'Staff') {
                    header("Location: /Webdev2/LibrarySystem/view/staff/ReservationSuccess.php?success=Approved");
                } else {
                    header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?success=Reservation+Fulfilled");
                }
            } else {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?error=Failed+to+Fulfill");
            }
            exit;

        case 'add':
            $user_id = $_POST['user_id'] ?? null;
            $book_id = $_POST['book_id'] ?? null;
            $reservation_date = $_POST['reservation_date'] ?? date('Y-m-d');
            $expiry_date = $_POST['expiry_date'] ?? date('Y-m-d', strtotime('+7 days'));
            $status = 'Pending';

            $isTeacher = (isset($_POST['source']) && $_POST['source'] === 'teacher');
            
            $redirectTarget = $isTeacher 
                ? "/Webdev2/LibrarySystem/view/Teacher/reservation.php" 
                : "/Webdev2/LibrarySystem/view/User/Reservation.php";

       
            if (!$isTeacher) {
                $activeReservations = $reservationModel->countActiveReservationsByUser($user_id);
                if ($activeReservations >= 2) {
                    header("Location: " . $redirectTarget . "?error=You+can+only+reserve+up+to+2+books");
                    exit;
                }
            }

            if ($reservationModel->addReservation($user_id, $book_id, $reservation_date, $expiry_date, $status)) {
                
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'Staff') {
                    header("Location: /Webdev2/LibrarySystem/view/staff/ReservationSuccess.php?success=Reservation+Added");
                } else {
                 
                    header("Location: " . $redirectTarget . "?success=Reservation+Added");
                }

            } else {
                header("Location: " . $redirectTarget . "?error=Failed+to+Add");
            }
            exit;

        case 'delete':
            if ($reservationModel->deleteReservation($reservation_id)) {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?success=Reservation+Deleted");
            } else {
                header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?error=Failed+to+Delete");
            }
            exit;

        default:
            header("Location: /Webdev2/LibrarySystem/view/Staff/ManageBorrowReservation.php?error=Invalid+Action");
            exit;
    }
}
?>