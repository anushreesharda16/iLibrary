<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../../../includes/head.php';
include '../../../includes/navbar.php';
require_once '../../../models/category.php';

$category = new Category();

// Redirect 
if (!isset($_GET['id'])) {
    header('Location:../manageCategories.php?msg=' . urlencode("Invalid request."));
    exit;
}

$id = (int)$_GET['id'];

// Fetch category details
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = $category->conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cat = $result->fetch_assoc();
$stmt->close();

// No category found
if (!$cat) {
    header('Location:../manageCategories.php?msg=' . urlencode("Category not found."));
    exit;
}

// Update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $category->updateCategory($id, $name);
    } else {
        $error = "Category name cannot be empty.";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4 text-center">Edit Category</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($cat['name']) ?>" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../manageCategories.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
