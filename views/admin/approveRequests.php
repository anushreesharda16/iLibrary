<?php
if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}
include '../../models/book.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'], $_POST['user_id'])) {
    $bookId = (int) $_POST['book_id'];
    $userId = (int) $_POST['user_id'];

    $book = new Book();
    $success = $book->approveIssueRequest($bookId, $userId);

    $_SESSION['message'] = $success ? "Request approved and book issued successfully." : "Failed to approve request.";
}

header("Location:./dashboard.php");
exit;
