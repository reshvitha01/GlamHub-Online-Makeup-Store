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

function currentUserRole()
{
    return $_SESSION['user_role'] ?? 'guest';
}

function hasRole($roles)
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    return isLoggedIn() && in_array(currentUserRole(), $roles, true);
}

function requireRole($roles)
{
    if (!hasRole($roles)) {
        header('Location: login.php?restricted=1');
        exit;
    }
}
?>
