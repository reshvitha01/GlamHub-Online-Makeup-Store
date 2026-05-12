<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'My Orders';
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Order History</p>
            <h1 class="section-title mb-0">My Orders</h1>
        </div>
        <a href="products.php" class="btn btn-outline-berry align-self-md-end">Shop More</a>
    </div>

    <?php if (isset($_GET['placed'])): ?>
        <div class="alert alert-success">Your order has been placed successfully.</div>
    <?php endif; ?>

    <?php if (!$orders): ?>
        <div class="soft-panel p-5 text-center">
            <span class="feature-icon mb-3 mx-auto"><i class="bi bi-receipt"></i></span>
            <h2 class="h4 fw-bold">No orders yet</h2>
            <p class="text-muted">Your completed orders will appear here.</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();
            ?>
            <div class="soft-panel p-4 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 border-bottom pb-3 mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Order #<?php echo $order['id']; ?></h2>
                        <p class="text-muted mb-0"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="text-md-end">
                        <span class="badge badge-glam mb-2"><?php echo htmlspecialchars($order['status']); ?></span>
                        <p class="price mb-0">RM <?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                </div>

                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between py-2">
                        <span><?php echo htmlspecialchars($item['product_name']); ?> · <?php echo htmlspecialchars($item['shade']); ?> x <?php echo $item['quantity']; ?></span>
                        <strong>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
