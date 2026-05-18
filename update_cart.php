<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$cartId = (int)($_POST['cart_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

$stmt = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?');
$stmt->execute([$quantity, $cartId, $_SESSION['user_id']]);

header('Location: cart.php?updated=1');
exit;
?>
