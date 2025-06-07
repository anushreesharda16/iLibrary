<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../../../includes/head.php';
include '../../../includes/navbar.php';
require_once '../../../models/category.php';

$addCategory = new Category();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    
    if(!empty($name)) {
        $addCategory->addCategory($name);
    }
    else {
        $error = "Category name cannot be empty.";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4 text-center">Add New Category</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../manageCategories.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'  ?>