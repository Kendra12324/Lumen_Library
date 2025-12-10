<?php
require_once __DIR__ . '/../model/Borrow.php';

$borrow = new Borrow();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $borrowID = $_POST['borrow_id'] ?? '';

    switch ($action) {
        case 'markReturned':
            $borrow->markReturned($borrowID);
            break;
        case 'renew':
            $borrow->renewBorrow($borrowID);
            break;
        case 'markOverdue':
                $borrow->markOverdue($borrowID);
                // Automatically apply penalty when marking as overdue
                $borrow->applyPenalty($borrowID, 50); 
                break;
        case 'markLost':
            $borrow->markLost($borrowID);
            break;
        }

    header('Location: ../view/Staff/ManageBorrowReservation.php');

    exit;
}
