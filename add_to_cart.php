<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$productId = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

$stmt = $pdo->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$productId]);

if (!$stmt->fetch()) {
    header('Location: products.php');
    exit;
}

$check = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
$check->execute([$_SESSION['user_id'], $productId]);
$item = $check->fetch();

if ($item) {
    $update = $pdo->prepare('UPDATE cart_items SET quantity = quantity + ? WHERE id = ?');
    $update->execute([$quantity, $item['id']]);
} else {
    $insert = $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)');
    $insert->execute([$_SESSION['user_id'], $productId, $quantity]);
}

header('Location: cart.php?added=1');
exit;
?>
