<?php
include '../includes/head.php';
include '../includes/navbar.php';
include '../models/contact.php';

$msg = "";
$alertType = "";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $cont = new Contact();
    $result = $cont->contactStore($email, $subject, $message);

    if ($result) {
        $msg = "Thank you for contacting us!";
        $alertType = 'success';
    } else {
        $msg = "Sorry we could not hear you. Please fill the form again.";
        $alertType = 'error';
    }
    // header('Location:contactUs.php');
    // exit;
}
?>

<div class="d-flex justify-content-center mb-3">
    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show w-50" role="alert" style="margin-top: 5px" >
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<div class="contact-section">
    <div class="container">
        <div class="row contact-card align-items-center">
            <!-- Left side text -->
            <div class="col-md-5 mb-4 mb-md-0">
                <h3 class="contact-heading mb-3">Have any inquiry?</h3>
                <p>Feel free to get in touch with us anytime. We’d love to help you!</p>
                <p><i class="bi bi-envelope me-2"></i> support@ilibrary.com</p>
                <p><i class="bi bi-telephone me-2"></i> +91-9876543210</p>
            </div>

            <!-- Right side form -->
            <div class="col-md-7">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="5" placeholder="Write your message..." required></textarea>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($success)): ?>
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
<?php if (!empty($error)): ?>
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

<?php include '../includes/footer.php'; ?>