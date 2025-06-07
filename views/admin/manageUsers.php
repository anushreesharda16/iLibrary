<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../includes/head.php';
include '../../includes/navbar.php';
require_once '../../models/manageUsers.php';


if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}
$manageUser = new UserManage();

// print_r(base_url);
// exit;
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['perPage']) && $_GET['perPage'] > 0 ? $_GET['perPage'] : 10;

$users = $manageUser->listUsers($search, $sort, $page, $perPage);

$totalRows = $manageUser->totalUsers($search);
$totalPages = ceil((int)$totalRows / (int)$perPage);

$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>


<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>

        <div class="w-50 mx-auto" style="margin-top: 8px;">
            <?php if ($msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>


        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2 class="mb-4">Manage Users</h2>

            <!-- Search & Sort -->
            <form method="GET" class="row g-3 mb-4">
                <div class="d-flex">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search users by name, email or role..." class="form-control me-2" />

                    <select name="sort" class="form-control me-2" onchange="this.form.submit()">
                        <option value="">Sort By</option>
                        <option value="az" <?= $sort == 'az' ? 'selected' : '' ?>>A-Z</option>
                        <option value="za" <?= $sort == 'za' ? 'selected' : '' ?>>Z-A</option>
                        <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Latest</option>
                        <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Oldest</option>
                    </select>

                    <select name="perPage" class="form-control me-2" onchange="this.form.submit()">
                        <option value="">Users per page</option>
                        <?php foreach ([10, 20, 30, 40, 50] as $num): ?>
                            <option value="<?= $num ?>" <?= $perPage == $num ? 'selected' : '' ?>><?= $num ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>

            <!-- User Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                                <td>
                                    <form action="./users/toggleStatus.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="current_status" value="<?= $user['status'] ?>">
                                        <button type="submit" name="toggle_status" class=" btn btn-sm <?= $user['status'] === 'active' ? 'btn-success' : 'btn-secondary' ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="./users/editUser.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="./users/deleteUser.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete this user?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-muted text-center">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&perPage=<?= $perPage ?>&sort=<?= urlencode($sort) ?>&search=<?= urlencode($search) ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&perPage=<?= $perPage ?>&sort=<?= urlencode($sort) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= min($totalPages, $page + 1) ?>&perPage=<?= $perPage ?>&sort=<?= urlencode($sort) ?>&search=<?= urlencode($search) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>