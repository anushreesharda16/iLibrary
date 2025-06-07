<?php
if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}
include '../../includes/head.php';
include '../../includes/navbar.php';
include '../../models/book.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}
$book = new Book();
$requests = $book->listPendingRequests();
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?> <!-- Sidebar links -->

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2 class="mb-4">Approve Book Requests to Issue</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info text-center">
                    <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <?php if (count($requests) > 0): ?>
                <div class="table-responsive shadow rounded">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>S.No</th>
                                <th>Book Title</th>
                                <th>Requested By</th>
                                <th>Request Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center align-middle">
                            <?php foreach ($requests as $index => $request): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($request['book_title']) ?></td>
                                    <td><?= htmlspecialchars($request['user_name']) ?></td>
                                    <td><?= date('d M Y, h:i A', strtotime($request['request_date'])) ?></td>
                                    <td>
                                        <form action="./approveRequests.php" method="POST" class="d-inline">
                                            <input type="hidden" name="book_id" value="<?= $request['book_id'] ?>">
                                            <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted fs-5 mt-5">
                    No pending requests to approve at the moment.
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php';?> 
