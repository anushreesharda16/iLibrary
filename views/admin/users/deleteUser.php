<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../../includes/head.php';
include '../../../includes/navbar.php';
require_once '../../../models/manageUsers.php';

if (!isset($_SESSION['user'])) {
    header('Location:../../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = (int)$_POST['id'];
    $manageUser = new UserManage();

    $result = $manageUser->deleteUser($userId);

    if ($result === null) {
        // Deletion successful
        header("Location: ../manageUsers.php?msg=" . urlencode("User deleted successfully."));
    } else {
        //error or some issue
        header("Location: ../manageUsers.php?error=" . urlencode($result));
    }
    exit;
} else {
    header("Location: ../manageUsers.php?error=" . urlencode("Invalid delete request."));
    exit;
}
?>