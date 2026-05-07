<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function currentUserName()
{
    return $_SESSION['user_name'] ?? 'Guest';
}
?>
