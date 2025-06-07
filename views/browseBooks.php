<?php
session_start();
include '../includes/head.php';
include '../includes/navbar.php';
require_once '../models/browseBooks.php';

// if(!isset($_SESSION['user'])){
//   header('Location:../auth/login.php');
// }
$bookObj = new Book();

// Get books from your method (which will handle search/sort/pagination)
$books = $bookObj->browseBooks();

?>

<style>
  .book-card {
    transition: transform 0.2s ease;
    cursor: pointer;
  }
  .book-card:hover {
    transform: scale(1.03);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  }
  .book-cover {
    height: 220px;
    object-fit: contain;
    background-color: #f8f9fa;
  }
</style>

<main class="container py-5">

  <h2 class="mb-4 text-center">Browse Books</h2>

  <!-- Search & Sort Form -->
  <form method="GET" action="browseBooks.php" class="row mb-4 g-2 justify-content-center">
    <div class="col-md-6">
      <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" placeholder="Search by title or author">
    </div>
    <div class="col-md-3">
      <select name="sort" class="form-select">
        <option value="title_asc" <?= (($_GET['sort'] ?? '') === 'title_asc') ? 'selected' : '' ?>>Title (A-Z)</option>
        <option value="title_desc" <?= (($_GET['sort'] ?? '') === 'title_desc') ? 'selected' : '' ?>>Title (Z-A)</option>
        <option value="author_asc" <?= (($_GET['sort'] ?? '') === 'author_asc') ? 'selected' : '' ?>>Author (A-Z)</option>
        <option value="author_desc" <?= (($_GET['sort'] ?? '') === 'author_desc') ? 'selected' : '' ?>>Author (Z-A)</option>
        <option value="newest" <?= (($_GET['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Apply</button>
    </div>
  </form>

  <!-- Books Grid -->
  <div class="row g-4">

    <?php if (empty($books)): ?>
      <p class="text-center">No books found.</p>
    <?php else: ?>
      <?php foreach ($books as $book): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card book-card h-100">
            <!-- <img src="<?= htmlspecialchars($book['coverImage']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="card-img-top book-cover"> -->
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
              <p class="card-text text-muted mb-1">By <?= htmlspecialchars($book['author']) ?></p>
              <p class="card-text"><small class="text-secondary"><?= htmlspecialchars($book['category']) ?></small></p>
              <p class="card-text flex-grow-1"><?= htmlspecialchars($book['description']) ?></p>
              <!-- <a href="viewBook.php?id=<?= $book['id'] ?>" class="btn btn-primary mt-auto">View Details</a> -->
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <!-- Pagination placeholder (if you add pagination in your method) -->
  <!--
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center mt-4">
      <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $currentPage - 1 ?>">Previous</a>
      </li>
      <li class="page-item active"><a class="page-link" href="#"><?= $currentPage ?></a></li>
      <li class="page-item <?= ($currentPage == $totalPages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next</a>
      </li>
    </ul>
  </nav>
  -->

</main>

<?php include '../includes/footer.php'; ?>
