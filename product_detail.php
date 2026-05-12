<?php
require_once 'includes/db.php';

$pageTitle = 'Product Details';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare('SELECT products.*, categories.name AS category_name
                       FROM products
                       INNER JOIN categories ON products.category_id = categories.id
                       WHERE products.id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <?php if (!$product): ?>
        <div class="alert alert-danger">Product not found.</div>
        <a href="products.php" class="btn btn-outline-berry">Back to Products</a>
    <?php else: ?>
        <div class="row g-5 align-items-start">
            <div class="col-lg-6">
                <img class="img-fluid rounded-3 shadow-sm w-100" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-lg-6">
                <span class="badge badge-glam mb-3"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 class="section-title mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price fs-3 mb-3">RM <?php echo number_format($product['price'], 2); ?></p>
                <p class="lead text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

                <div class="soft-panel p-4 my-4">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <p class="text-muted mb-1">Shade</p>
                            <p class="fw-bold mb-0"><?php echo htmlspecialchars($product['shade']); ?></p>
                        </div>
                        <div class="col-sm-4">
                            <p class="text-muted mb-1">Skin Tone</p>
                            <p class="fw-bold mb-0"><?php echo htmlspecialchars($product['skin_tone']); ?></p>
                        </div>
                        <div class="col-sm-4">
                            <p class="text-muted mb-1">Undertone</p>
                            <p class="fw-bold mb-0"><?php echo htmlspecialchars($product['undertone']); ?></p>
                        </div>
                    </div>
                </div>

                <?php if (isLoggedIn()): ?>
                    <form method="post" action="add_to_cart.php" class="d-flex flex-wrap gap-3 align-items-end"><input type="hidden" name="product_id" value="<?php echo $product['id']; ?>"><div><label class="form-label fw-semibold">Quantity</label><input type="number" name="quantity" class="form-control" value="1" min="1" style="width: 110px;"></div><button type="submit" class="btn btn-berry btn-lg"><i class="bi bi-bag-plus me-2"></i>Add to Cart</button></form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Please <a href="login.php">login</a> to access purchase features.
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="products.php" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>Back to products
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
