<?php
if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once '../../../models/book.php';

$bookObj = new Book();
if(!isset($_GET['id']))
{
    header('Location:../manageBooks.php?msg=' . urlencode("Invalid book ID"));
}

$result = $bookObj->deleteBook($_GET['id']);
if($result !== true)
{
    header('Location:../manageBooks.php?msg=' . urlencode($result));
    exit;
}

header('Location:../manageBooks.php?msg=' . urlencode("Book Deleted Successfully"));
exit;



?>