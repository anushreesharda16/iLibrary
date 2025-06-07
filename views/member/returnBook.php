<?php
require_once '../../models/userIssueBook.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['book_id']) || !isset($_SESSION['user'])) {
    header("Location:./listIssuedBooks.php");
    exit;
}

$bookId = intval($_GET['book_id']);
$userId = $_SESSION['user']['id'];

$obj = new UserIssueBook(); 

if ($obj->returnBook($userId, $bookId)) {
    $_SESSION['message'] = "Book returned successfully!";
} else {
    $_SESSION['error'] = "Failed to return the book.";
}

header("Location:./listIssuedBooks.php");
exit;
?>