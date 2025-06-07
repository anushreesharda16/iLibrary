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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../manageBooks.php?msg=" . urlencode("Invalid Book ID"));
    exit;
}

$book_id = $_GET['id'];
$book = $bookObj->getBookById($book_id);

if (!$book) {
    header("Location: ../manageBooks.php?msg=" . urlencode("Book not found"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>'; print_r($_POST); echo '</pre>';
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $status = $_POST['status'] ?? 'available';
    $newImages = $_FILES['images'] ?? [];
    $deleteImages = $_POST['delete_images'] ?? [];

    $result = $bookObj->updateBook($book_id, $title, $author, $category_id, $status, $newImages, $deleteImages);

    if ($result !== true) {
        $error = $result;
    }
}
?>

<div class="container mt-4" style="margin-left: 250px;">
    <h3 class="mb-4">Edit Book</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Book Title</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($book['title']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" required value="<?= htmlspecialchars($book['author']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $book['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <?php $status = strtolower($book['status']); ?>
                <?php $status = strtolower($book['status']); ?>
                <option value="available" <?= $status === 'available' ? 'selected' : '' ?>>Available</option>
                <option value="issued" <?= $status === 'issued' ? 'selected' : '' ?>>Issued</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Upload New Images (optional)</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>

        <div class="col-12">
            <label class="form-label">Current Images</label><br>
            <?php foreach ($book['images'] as $img): ?>
                <div class="form-check form-check-inline text-center">
                    <img src="../../../uploads/book_cover_images/<?= htmlspecialchars($img) ?>" width="50" height="60" class="mb-1 d-block border">
                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($img) ?>" id="<?= $img ?>">
                    <label class="form-check-label small" for="<?= $img ?>">Delete</label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="col-12">
            <button class="btn btn-primary" type="submit">Update Book</button>
            <a href="../manageBooks.php" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>

<?php include '../../../includes/footer.php'; ?>