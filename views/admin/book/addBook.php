<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../../includes/head.php';
include '../../../includes/navbar.php';
include '../../../includes/sidebar.php';

require_once '../../../models/book.php';
require_once '../../../models/category.php';

$bookObj = new Book();
$categoryObj = new Category();

$categories = $categoryObj->getAllCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $status = $_POST['status'] ?? 'available';
    $images = $_FILES['images'];

    $result = $bookObj->addBook($title, $images, $author, $category_id, $status);

    if ($result !== true) {
        $error = $result;
    }
}
?>

<div class="container mt-4" style="margin-left: 250px;">
    <h3 class="mb-4">Add Book</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="title" class="form-label">Book Title</label>
            <input type="text" name="title" id="title" required class="form-control">
        </div>
        <div class="col-md-6">
            <label for="author" class="form-label">Author</label>
            <input type="text" name="author" id="author" required class="form-control">
        </div>
        <div class="col-md-6">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" required class="form-select">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="available">Available</option>
                <option value="issued">Issued</option>
            </select>
        </div>
        <div class="col-12">
            <label for="images" class="form-label">Upload Book Images (jpg/jpeg/png)</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple required>
        </div>
        <div class="col-12">
            <button class="btn btn-success" type="submit">Add Book</button>
            <a href="../manageBooks.php" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>

<?php include '../../../includes/footer.php'; ?>
