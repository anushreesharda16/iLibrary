<?php
session_start();
include '../../models/userIssueBook.php';
include '../../config/constants.php';

if (!isset($_SESSION['user'])) {
    header('Location:../auth/login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$bookId = $_GET['book_id'] ?? null;

// print_r($bookId);
// exit;
if (!$bookId) {
    $_SESSION['error'] = "Invalid book selected.";
    header('Location:./listAvailableBooks.php');
    exit();
}

$bookObj = new UserIssueBook();
$issued = $bookObj->issueBook($userId, $bookId);

if ($issued) {
    $_SESSION['success'] = "Book issued successfully! Awaiting admin approval.";
} else {
    $_SESSION['error'] = "Failed to issue the book. Please try again.";
}

header('Location: ./listAvailableBooks.php');
exit();
