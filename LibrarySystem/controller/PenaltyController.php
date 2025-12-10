<?php
session_start();
require_once __DIR__ . '/../model/Penalty.php';



if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/login.php');

    exit;
}

$penaltyModel = new Penalty();

// ✅ Handle actions (mark as paid, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $penaltyID = $_POST['penalty_id'] ?? null;

    if ($action === 'mark_paid' && $penaltyID) {
        $penaltyModel->markAsPaid($penaltyID);
    } elseif ($action === 'delete' && $penaltyID) {
        $penaltyModel->deletePenalty($penaltyID);
    }

  
        header('Location: ../view/Staff/ManagePenalty.php');
    exit;
}

$penalties = $penaltyModel->getAllPenalties();
?>
