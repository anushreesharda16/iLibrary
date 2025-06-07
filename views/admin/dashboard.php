<?php
session_start();
include '../../includes/head.php';
include '../../includes/navbar.php';
include '../../models/book.php';
include '../../models/manageUsers.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}

$bookObj = new Book();
$userObj = new UserManage();

$totalBooks = $bookObj->countBooks();
$activeUsers = $userObj->totalActiveUsers();
$inactiveUsers = $userObj->totalInactiveUsers();
$issuedBooks = $bookObj->totalIssuedBooks();
$pendingRequests = $bookObj->totalPendingRequests();
$overdueBooks = $bookObj->totalOverdueBooks();

$recentRequests = $bookObj->listRecentPendingRequests();


// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: ../../auth/login.php');
//     exit();
// }
?>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-2 bg-dark text-white vh-100 p-0">
      <?php include '../../includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="col-md-10">
      <div class="container mt-4">
        <!-- Dashboard Cards -->
        <div class="row text-center">
          <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary shadow">
              <div class="card-body">
                <h5><i class="bi bi-book"></i> Total Books</h5>
                <p class="card-text fs-3"><?= $totalBooks ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-white bg-success shadow">
              <div class="card-body">
                <h5><i class="bi bi-person-check"></i> Active Users</h5>
                <p class="card-text fs-3"><?= $activeUsers ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-white bg-secondary shadow">
              <div class="card-body">
                <h5><i class="bi bi-person-x"></i> Inactive Users</h5>
                <p class="card-text fs-3"><?= $inactiveUsers ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-white bg-info shadow">
              <div class="card-body">
                <h5><i class="bi bi-journal-check"></i> Issued Books</h5>
                <p class="card-text fs-3"><?= $issuedBooks ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning shadow">
              <div class="card-body">
                <h5><i class="bi bi-hourglass-split"></i> Pending Requests</h5>
                <p class="card-text fs-3"><?= $pendingRequests ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger shadow">
              <div class="card-body">
                <h5><i class="bi bi-exclamation-circle-fill me-2"></i> OverDue Books</h5>
                <p class="card-text fs-3"><?= $overdueBooks ?></p>
              </div>
            </div>
          </div>
        </div>


        <!-- Recent Requests Table -->

        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-info text-center">
            <?= $_SESSION['message'];
            unset($_SESSION['message']); ?>
          </div>
        <?php endif; ?>


        <div class="card mt-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Recent Book Issue Requests</h5>
          </div>
          <div class="card-body p-0">
            <?php if (!empty($recentRequests)): ?>
              <div class="table-responsive">
                <table class="table mb-0 table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>User Name</th>
                      <th>Book Title</th>
                      <th>Request Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recentRequests as $index => $request): ?>
                      <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($request['user_name']) ?></td>
                        <td><?= htmlspecialchars($request['book_title']) ?></td>
                        <td><?= date('d M Y', strtotime($request['request_date'])) ?></td>
                        <td><span class="badge bg-warning text-dark"><?= ucfirst($request['status']) ?></span></td>
                        <td>
                          <form action="./approveRequests.php" method="POST" class="d-inline">
                            <input type="hidden" name="book_id" value="<?= $request['book_id'] ?>">
                            <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                              <i class="bi bi-check-circle"></i> Approve
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted text-center p-4">No pending requests found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>