<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireRole('admin');

$pageTitle = 'All Orders';
$status = $_GET['status'] ?? '';
$allowedStatuses = ['Pending', 'Processing', 'Packed', 'Completed', 'Cancelled'];

$sql = 'SELECT orders.*, users.full_name, users.email FROM orders INNER JOIN users ON orders.user_id = users.id WHERE 1 = 1';
$params = [];
if ($status !== '' && in_array($status, $allowedStatuses, true)) {
    $sql .= ' AND orders.status = ?';
    $params[] = $status;
}
$sql .= ' ORDER BY orders.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Admin Panel</p>
            <h1 class="section-title mb-0">All Customer Orders</h1>
        </div>
        <a href="admin.php" class="btn btn-outline-berry align-self-lg-end">Dashboard</a>
    </div>

    <form class="soft-panel filter-panel p-3 mb-4" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filter by Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <?php foreach ($allowedStatuses as $item): ?>
                        <option value="<?php echo $item; ?>" <?php echo $status === $item ? 'selected' : ''; ?>><?php echo $item; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-berry">Apply Filter</button>
            </div>
            <div class="col-md-3 d-grid">
                <a href="admin_orders.php" class="btn btn-outline-berry">Clear</a>
            </div>
        </div>
    </form>

    <?php if (!$orders): ?>
        <div class="alert alert-warning">No orders found.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();
            ?>
            <div class="soft-panel p-4 mb-4">
                <div class="row g-3 border-bottom pb-3 mb-3">
                    <div class="col-md-3">
                        <h2 class="h5 fw-bold mb-1">Order #<?php echo $order['id']; ?></h2>
                        <p class="text-muted mb-0"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="fw-bold mb-1"><?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($order['email']); ?></p>
                    </div>
                    <div class="col-md-3">
                        <span class="badge badge-glam"><?php echo htmlspecialchars($order['status']); ?></span>
                    </div>
                    <div class="col-md-2 text-md-end">
                        <p class="price mb-0">RM <?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                </div>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between py-1">
                        <span><?php echo htmlspecialchars($item['product_name']); ?> &middot; <?php echo htmlspecialchars($item['shade']); ?> x <?php echo $item['quantity']; ?></span>
                        <strong>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
