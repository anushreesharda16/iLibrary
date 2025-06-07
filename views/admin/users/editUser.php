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

$manageUser = new UserManage();

if (!isset($_GET['id'])) {
    header('Location: ../manageUsers.php?msg=' . urlencode("Invalid user ID"));
    exit;
}

$userId = (int)$_GET['id'];
$user = $manageUser->userById($userId);

if (!$user) {
    header('Location: ../manageUsers.php?msg=' . urlencode("User not found"));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedName = trim($_POST['name']);

    if (!empty($updatedName)) {
        $manageUser->updateUser($userId, $updatedName);
        // Redirect handled inside updateUser()
    } else {
        $error = "Name cannot be empty.";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4">Edit User</h3>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">User Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="../manageUsers.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
