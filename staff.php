<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole(['staff', 'admin']);

$pageTitle = 'Staff Page';
$success = '';
$allowedStatuses = ['Pending', 'Processing', 'Packed', 'Completed', 'Cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';

    if (in_array($status, $allowedStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
        $success = 'Order status updated successfully.';
    }
}

$orders = $pdo->query('SELECT orders.*, users.full_name, users.email
                      FROM orders
                      INNER JOIN users ON orders.user_id = users.id
                      ORDER BY orders.created_at DESC')->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Staff Page</p>
            <h1 class="section-title mb-0">Order Management</h1>
        </div>
        <a href="admin.php" class="btn btn-outline-berry align-self-lg-end"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto"><i class="bi bi-clipboard-data"></i></span>
                <h2 class="h5 fw-bold">Check Orders</h2>
                <p class="text-muted mb-0">View customer orders, delivery details, and order totals.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto"><i class="bi bi-truck"></i></span>
                <h2 class="h5 fw-bold">Update Status</h2>
                <p class="text-muted mb-0">Change orders from pending to processing, packed, or completed.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto"><i class="bi bi-person-heart"></i></span>
                <h2 class="h5 fw-bold">Support Customers</h2>
                <p class="text-muted mb-0">Use customer contact and delivery details for order follow-up.</p>
            </div>
        </div>
    </div>

    <?php if (!$orders): ?>
        <div class="soft-panel p-5 text-center">
            <span class="feature-icon mb-3 mx-auto"><i class="bi bi-inbox"></i></span>
            <h2 class="h4 fw-bold">No orders yet</h2>
            <p class="text-muted mb-0">Customer orders will appear here after checkout.</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();
            ?>
            <div class="soft-panel p-4 mb-4">
                <div class="row g-3 align-items-start border-bottom pb-3 mb-3">
                    <div class="col-lg-4">
                        <h2 class="h5 fw-bold mb-1">Order #<?php echo $order['id']; ?></h2>
                        <p class="text-muted mb-1"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                        <p class="price mb-0">RM <?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    <div class="col-lg-4">
                        <p class="fw-bold mb-1"><?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($order['email']); ?></p>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($order['delivery_phone']); ?></p>
                    </div>
                    <div class="col-lg-4">
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" class="form-select">
                                <?php foreach ($allowedStatuses as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-berry">Update</button>
                        </form>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-7">
                        <h3 class="h6 fw-bold">Items</h3>
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex justify-content-between py-1">
                                <span><?php echo htmlspecialchars($item['product_name']); ?> · <?php echo htmlspecialchars($item['shade']); ?> x <?php echo $item['quantity']; ?></span>
                                <strong>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-lg-5">
                        <h3 class="h6 fw-bold">Delivery Address</h3>
                        <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>

