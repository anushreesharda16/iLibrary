<?php
include '../../models/userIssueBook.php';
include '../../config/constants.php';
include '../../includes/head.php';
include '../../includes/navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}    

$obj = new UserIssueBook();

$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

$issuedBooks = $obj->listIssuedBooks();
?>

<div class="container mt-4">
    <h3 class="mb-4">Books You've Issued</h3>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($issuedBooks as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['category']) ?></td>
                    <td><?= htmlspecialchars($book['due_date']) ?></td>
                    <td>
                        <a href="./returnBook.php?book_id=<?= $book['book_id'] ?>" class="btn btn-sm btn-danger">Return</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
<?php require_once '../../includes/footer.php'; ?>
