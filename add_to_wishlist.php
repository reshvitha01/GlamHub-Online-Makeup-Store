<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$productId = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
if ($productId > 0) {
    $stmt = $pdo->prepare('INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], $productId]);
}

header('Location: wishlist.php?added=1');
exit;
?>
