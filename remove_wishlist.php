<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM wishlist WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);

header('Location: wishlist.php?removed=1');
exit;
?>
