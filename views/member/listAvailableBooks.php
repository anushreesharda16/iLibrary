<?php
include '../../models/userIssueBook.php';
include '../../models/book.php';
include '../../models/category.php';
// include '../../config/constants.php';
include '../../includes/head.php';
include '../../includes/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}

$obj = new UserIssueBook();
$bookObj = new Book();
$categoryObj = new Category();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['perPage']) && $_GET['perPage'] > 0 ? (int)$_GET['perPage'] : 6;
$categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;

$listBooks = $obj->listAvailableBooks($search, $sort, $categoryId, $perPage, $page);
$totalRows = $bookObj->getAllAvailableBooks($search, $categoryId);
$totalPages = ceil((int)$totalRows / $perPage);
$categories = $categoryObj->getAllCategories();
?>

<div class="container mt-4">
    <h3 class="mb-4">Available Books</h3>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search by title/author/category" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="">Sort by</option>
                <option value="az" <?= $sort == 'az' ? 'selected' : '' ?>>A-Z</option>
                <option value="za" <?= $sort == 'za' ? 'selected' : '' ?>>Z-A</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="perPage" class="form-select" onchange="this.form.submit()">
                <?php foreach ([5, 15, 25] as $n): ?>
                    <option value="<?= $n ?>" <?= $perPage == $n ? 'selected' : '' ?>><?= $n ?> per page</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


    <div class="row">
        <?php foreach ($listBooks as $book): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($book['images'])): ?>
                        <img src="<?= base_url . 'uploads/book_cover_images/' . htmlspecialchars($book['images'][0]) ?>" class="card-img-top" style="height: 200px; object-fit: contain; background-color: #f8f9fa;">
                    <?php else: ?>
                        <img src="<?= base_url ?>assets/images/no-image.png" class="card-img-top" style="height: 200px; object-fit: contain;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                        <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($book['category']) ?></p>
                        <a href="./issueBook.php?book_id=<?= $book['id'] ?>" class="btn btn-success btn-sm">Issue Book</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($listBooks)): ?>
            <div class="alert alert-info text-center">No books found matching your criteria.</div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&perPage=<?= $perPage ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&category=<?= $categoryId ?>">Previous</a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&perPage=<?= $perPage ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&category=<?= $categoryId ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($totalPages, $page + 1) ?>&perPage=<?= $perPage ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&category=<?= $categoryId ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<?php include_once '../../includes/footer.php'; ?>