<?php
include '../../../models/Category.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $categoryId = (int) $_POST['id'];

    $category = new Category();
    $category->deleteCategory($categoryId);
} else {
    header('Location: ../manageCategories.php?msg=' . urlencode('Invalid request.'));
    exit;
}
