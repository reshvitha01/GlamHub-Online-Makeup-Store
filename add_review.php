<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$productId = (int)($_POST['product_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($productId > 0 && $rating >= 1 && $rating <= 5 && $comment !== '') {
    $stmt = $pdo->prepare('INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $productId, $rating, $comment]);
    header('Location: product_detail.php?id=' . $productId . '&reviewed=1');
    exit;
}

header('Location: product_detail.php?id=' . $productId . '&review_error=1');
exit;
?>
