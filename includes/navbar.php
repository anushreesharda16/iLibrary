<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">iLibrary</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">

        <?php if (!isset($_SESSION['user'])): ?>
          <?php if ($currentPage === 'index.php'): ?>
            <li class="nav-item me-2"><a class="nav-link active" aria-current="page" href="<?= base_url ?>index.php">Home</a></li>
            <li class="nav-item me-2"><a class="nav-link" href="<?= base_url ?>views/contactUs.php"><i class="bi bi-envelope-fill me-2"></i>Contact Us</a></li>
            <li class="nav-item me-2"><a class="nav-link" href="<?= base_url ?>views/auth/login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= base_url ?>views/auth/register.php"><button class="btn btn-outline-info btn-sm"> Register </button></a></li>
          <?php elseif ($currentPage === 'contactUs.php'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url ?>index.php">Home</a></li>
          <?php endif; ?>


        <?php else: ?>
          <?php if ($_SESSION['user']['role'] === 'member'): ?>
            <?php if ($currentPage === 'dashboard.php' || $currentPage === 'listIssuedBooks.php'): ?>
              <li class="nav-item me-2"><a class="nav-link" href="<?= base_url ?>views/member/dashboard.php">Home</a></li>
              <li class="nav-item me-2"><a class="nav-link" href="<?= base_url ?>views/contactUs.php"><i class="bi bi-envelope-fill me-2"></i>Contact Us</a></li>
              <li class="nav-item me-2"><a class="nav-link" href="<?= base_url ?>views/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            <?php elseif ($currentPage === 'contactUs.php'): ?>
              <li class="nav-item"><a class="nav-link" href="<?= base_url ?>views/member/dashboard.php">Home</a></li>
            <?php endif; ?>
          <?php else: ?>
            <?php if ($currentPage === 'dashboard.php'): ?>
              <li class="nav-item"><a class="nav-link" href="<?= base_url ?>views/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>