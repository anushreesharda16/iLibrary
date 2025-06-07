<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../includes/head.php';
include '../../includes/navbar.php';
require_once '../../models/book.php';
require_once '../../models/category.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}

$bookObj = new Book();
$categoryObj = new Category();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$perPage = $_GET['perPage'] ?? 10;
$page = $_GET['page'] ?? 1;
$filter = [
    'category_name' => $_GET['category'] ?? '',
    'status' => $_GET['status'] ?? ''
];

$books = $bookObj->listBook($search, $sort, $filter, $perPage, $page);
$categories = $categoryObj->getAllCategories();

$countBook = $bookObj->countBooks();
$totalPages = ceil((int)$countBook / (int) $perPage);
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>

        <div class="col-md-10 ms-auto px-4 mt-4">
            <h2 class="mb-4">Manage Books</h2>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-info alert-dismissible fade show small-msg" role="alert">
                    <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form class="row g-3 mb-3" method="GET">
                <div class="col-md-3">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search books...">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>" <?= $filter['category_name'] === $cat['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="available" <?= $filter['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="issued" <?= $filter['status'] === 'issued' ? 'selected' : '' ?>>Issued</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select" onchange="this.form.submit()">

                        <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Latest</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title A-Z</option>
                        <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Title Z-A</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="perPage" class="form-select" onchange="this.form.submit()">
                        <?php foreach ([5, 10, 20, 50] as $val): ?>
                            <option value="<?= $val ?>" <?= $perPage == $val ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Apply</button>
                </div>
            </form>

            <div class="mb-3">
                <a href="./book/addBook.php" class="btn btn-success">Add Book</a>
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No books found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $index => $book): ?>
                            <tr>
                                <td><?= $book['id'] ?></td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td><?= htmlspecialchars($book['isbn']) ?></td>
                                <td><?= htmlspecialchars($book['category']) ?></td>
                                <td><?= htmlspecialchars($book['status']) ?></td>
                                <td>
                                    <?php foreach ($book['images'] as $img): ?>
                                        <img src="../../uploads/book_cover_images/<?= htmlspecialchars($img) ?>" width="30" height="40" class="me-1 mb-1">
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <a href="./book/editBook.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="./book/deleteBook.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

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
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>