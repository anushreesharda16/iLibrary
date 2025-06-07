<?php
include '../../config/constants.php';
if(session_status() === PHP_SESSION_NONE) {
    session_start();
 }
session_unset();
session_destroy();
header('Location: ' . base_url . 'index.php');
exit();
?>