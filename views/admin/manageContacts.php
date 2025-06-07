<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../includes/head.php';
include '../../includes/navbar.php';
include '../../models/contact.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
}

$sort = $_GET['sort'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = isset($_GET['perPage']) && $_GET['perPage'] > 0 ? $_GET['perPage'] : 10;

$objEnquiry = new Contact();
$enquiries = $objEnquiry->listEnquiries($sort, $perPage, $page);

$totalRows = $objEnquiry->countEnquiry();
$totalPages = ceil((int)$totalRows / (int)$perPage);



?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <div class="d-flex justify-content-center align-items-center mb-4">
                <h2 class="text-center m-0">Enquiries</h2>
            </div>

            <!-- Sort and pagination -->
            <form method="GET" class="row g-3 mb-4">
                <div class="d-flex justify-content-end">
                    <select name="sort" class="form-select form-select-sm me-3 w-auto" onchange="this.form.submit()">
                        <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Latest</option>
                        <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Oldest</option>
                    </select>

                    <select name="perPage" class="form-select form-select-sm me-3 w-auto" onchange="this.form.submit()">
                        <?php foreach ([10, 20, 30, 40, 50] as $num): ?>
                            <option value="<?= $num ?>" <?= $perPage == $num ? 'selected' : '' ?>><?= $num ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>


            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($enquiries as $index => $enquiry): ?>
                            <tr>
                                <td><?= htmlspecialchars($enquiry['id']) ?></td>
                                <td><?= htmlspecialchars($enquiry['email']) ?></td>
                                <td><?= htmlspecialchars($enquiry['subject']) ?></td>
                                <td><?= htmlspecialchars($enquiry['message']) ?></td>
                                <td><?= htmlspecialchars($enquiry['Date']) ?></td>
                                <!-- <td>
                                    <a href="./category/editCategory.php?id=<?= $category['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="./category/deleteCategory.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                    </form>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($enquiries) || $enquiries == 0): ?>
                            <tr>
                                <td colspan="3" class="text-muted text-center">No enquiries.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&perPage=<?= $perPage ?>&sort=<?= urlencode($sort) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&perPage=<?= $perPage ?>&sort=<?= urlencode($sort) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= min($totalPages, $page + 1) ?>&perPage=<?= $perPage ?>&sort=<?= urlencode($sort) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </main>
    </div>
</div>