<?php
include '../../../models/manageUsers.php';

if (isset($_POST['toggle_status']) && isset($_POST['user_id']) && isset($_POST['current_status'])) {
    $userId = (int)$_POST['user_id'];
    $currentStatus = $_POST['current_status'];
    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

    $obj = new UserManage();
    $result = $obj->setStatus($userId, $newStatus);

    if ($result) {
        // success
        header('Location: ../manageUsers.php?msg=' . urlencode("User status updated to $newStatus"));
    } else {
        // error
        header('Location: ../manageUsers.php?error=' . urlencode("Failed to update user status."));
    }
} else {
    header('Location: ../manageUsers.php?error=' . urlencode("Invalid request."));
}
?>