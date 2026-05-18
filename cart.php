<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Shopping Cart';
$stmt = $pdo->prepare('SELECT cart_items.id AS cart_id, cart_items.quantity, products.*
                       FROM cart_items
                       INNER JOIN products ON cart_items.product_id = products.id
                       WHERE cart_items.user_id = ?
                       ORDER BY cart_items.created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Shopping Cart</p>
            <h1 class="section-title mb-0">Your Selected Products</h1>
        </div>
        <a href="products.php" class="btn btn-outline-berry align-self-md-end">Continue Shopping</a>
    </div>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Product added to cart.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Cart quantity updated.</div>
    <?php endif; ?>
    <?php if (isset($_GET['removed'])): ?>
        <div class="alert alert-success">Item removed from cart.</div>
    <?php endif; ?>

    <?php if (!$items): ?>
        <div class="soft-panel p-5 text-center">
            <span class="feature-icon mb-3 mx-auto"><i class="bi bi-bag"></i></span>
            <h2 class="h4 fw-bold">Your cart is empty</h2>
            <p class="text-muted">Browse products and add your favourite makeup items.</p>
            <a href="products.php" class="btn btn-berry">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php foreach ($items as $item): ?>
                    <div class="soft-panel p-3 mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-4 col-md-3">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded-3">
                            </div>
                            <div class="col-8 col-md-4">
                                <h2 class="h5 fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h2>
                                <p class="text-muted mb-1"><?php echo htmlspecialchars($item['shade']); ?></p>
                                <p class="price mb-0">RM <?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="col-md-3">
                                <form method="post" action="update_cart.php" class="d-flex gap-2">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <input type="number" name="quantity" class="form-control form-control-sm" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="submit" class="btn btn-sm btn-outline-berry">Update</button>
                                </form>
                            </div>
                            <div class="col-md-2 text-md-end">
                                <p class="fw-bold mb-2">RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                <a href="remove_cart_item.php?id=<?php echo $item['cart_id']; ?>" class="btn btn-sm btn-outline-danger">Remove</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-4">
                <div class="soft-panel p-4 sticky-top" style="top: 95px;">
                    <h2 class="h4 fw-bold mb-3">Order Summary</h2>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total</span>
                        <strong>RM <?php echo number_format($total, 2); ?></strong>
                    </div>
                    <a href="checkout.php" class="btn btn-berry w-100">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
