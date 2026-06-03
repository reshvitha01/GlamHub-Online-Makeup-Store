<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('admin');

$pageTitle = 'Admin Dashboard';
$usersCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$productsCount = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$ordersCount = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalSales = (float)$pdo->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders')->fetchColumn();

$latestProducts = $pdo->query('SELECT products.*, categories.name AS category_name
                              FROM products
                              INNER JOIN categories ON products.category_id = categories.id
                              ORDER BY products.created_at DESC
                              LIMIT 5')->fetchAll();
$latestOrders = $pdo->query('SELECT orders.*, users.full_name
                            FROM orders
                            INNER JOIN users ON orders.user_id = users.id
                            ORDER BY orders.created_at DESC
                            LIMIT 5')->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Admin Panel</p>
            <h1 class="section-title mb-0">GlamHub Dashboard</h1>
        </div>
        <div class="d-flex flex-wrap gap-2 align-self-lg-end">
            <a href="admin_products.php" class="btn btn-berry"><i class="bi bi-box-seam me-2"></i>Manage Products</a>
            <a href="admin_orders.php" class="btn btn-outline-berry"><i class="bi bi-receipt me-2"></i>All Orders</a><a href="staff.php" class="btn btn-outline-berry"><i class="bi bi-clipboard-check me-2"></i>Staff Orders</a>
            <a href="manage_accounts.php" class="btn btn-outline-berry"><i class="bi bi-person-gear me-2"></i>Manage Accounts</a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-sm-6 col-lg-3">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-people"></i></span>
                <p class="text-muted mb-1">Customers</p>
                <h2 class="fw-bold mb-0"><?php echo $usersCount; ?></h2>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-bag-heart"></i></span>
                <p class="text-muted mb-1">Products</p>
                <h2 class="fw-bold mb-0"><?php echo $productsCount; ?></h2>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-receipt"></i></span>
                <p class="text-muted mb-1">Orders</p>
                <h2 class="fw-bold mb-0"><?php echo $ordersCount; ?></h2>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-cash-coin"></i></span>
                <p class="text-muted mb-1">Total Sales</p>
                <h2 class="fw-bold mb-0">RM <?php echo number_format($totalSales, 2); ?></h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="soft-panel p-4 h-100">
                <h2 class="h4 fw-bold mb-3">Latest Products</h2>
                <?php if (!$latestProducts): ?>
                    <p class="text-muted mb-0">No products available.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latestProducts as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($product['shade']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td>RM <?php echo number_format($product['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="soft-panel p-4 h-100">
                <h2 class="h4 fw-bold mb-3">Latest Orders</h2>
                <?php if (!$latestOrders): ?>
                    <p class="text-muted mb-0">No orders placed yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latestOrders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td><span class="badge badge-glam"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                        <td>RM <?php echo number_format($order['total_amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>