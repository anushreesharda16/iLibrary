<?php
include '../../models/UserAuth.php';
include '../../config/constants.php';
include '../../includes/head.php';
include '../../includes/navbar.php';

$obj = new User();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $message = $obj->login($email, $password);
    if ($message === 'Login successful') {
        $success = $message;
    } else {
        $error = $message;
    }
}

?>

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4 text-center">Login</h3>

    <form method="POST" action="">

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3">Create an account? <a href="./register.php">Register</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: <?= json_encode($message) ?>,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false
        });
    </script>
<?php endif; ?>
<?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'error',
            title: <?= json_encode($message) ?>,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false
        });
    </script>
<?php endif; ?>



<?php include '../../includes/footer.php'; ?>