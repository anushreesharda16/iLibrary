<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../includes/head.php';
include '../../includes/navbar.php';
require_once '../../models/category.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}

$categoryObj = new Category();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['perPage']) && $_GET['perPage'] > 0 ? $_GET['perPage'] : 10;

$categories = $categoryObj->listCategory($search, $sort, $page, $perPage);
$totalRows = $categoryObj->countCategories();
$totalPages = ceil((int)$totalRows / (int)$perPage);
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manage Categories</h2>
                <a href="./category/addCategory.php" class="btn btn-success">Add Category</a>
            </div>

            <!-- Search & Sort -->
            <form method="GET" class="row g-3 mb-4">
                <div class="d-flex">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search categories..." class="form-control me-2" />

                    <select name="sort" class="form-control me-2" onchange="this.form.submit()">
                        <option value="">Sort By</option>
                        <option value="az" <?= $sort == 'az' ? 'selected' : '' ?>>A-Z</option>
                        <option value="za" <?= $sort == 'za' ? 'selected' : '' ?>>Z-A</option>
                    </select>

                    <select name="perPage" class="form-control me-2" onchange="this.form.submit()">
                        <option value="">Categories per page</option>
                        <?php foreach ([10, 20, 30, 40, 50] as $num): ?>
                            <option value="<?= $num ?>" <?= $perPage == $num ? 'selected' : '' ?>><?= $num ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" name="apply" class="btn btn-primary">Apply</button>
                </div>
            </form>

            <!-- Message -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show w-auto mx-auto text-center small p-2 px-3" role="alert">
                    <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($categories as $index => $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['id']) ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td>
                                    <a href="./category/editCategory.php?id=<?= $category['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="./category/deleteCategory.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3" class="text-muted text-center">No categories found.</td>
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