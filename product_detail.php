<?php
require_once 'includes/db.php';

$pageTitle = 'Product Details';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT products.*, categories.name AS category_name
                       FROM products
                       INNER JOIN categories ON products.category_id = categories.id
                       WHERE products.id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

$reviews = [];
$averageRating = 0;
if ($product) {
    try {
        $reviewStmt = $pdo->prepare('SELECT reviews.*, users.username
                                     FROM reviews
                                     INNER JOIN users ON reviews.user_id = users.id
                                     WHERE reviews.product_id = ?
                                     ORDER BY reviews.created_at DESC');
        $reviewStmt->execute([$id]);
        $reviews = $reviewStmt->fetchAll();

        if ($reviews) {
            $averageRating = array_sum(array_column($reviews, 'rating')) / count($reviews);
        }
    } catch (PDOException $e) {
        $reviews = [];
    }
}

require_once 'includes/header.php';
?>

<section class="container py-5">
    <?php if (!$product): ?>
        <div class="alert alert-danger">Product not found.</div>
        <a href="products.php" class="btn btn-outline-berry">Back to Products</a>
    <?php else: ?>
        <?php if (isset($_GET['reviewed'])): ?>
            <div class="alert alert-success">Thank you. Your review has been added.</div>
        <?php endif; ?>
        <?php if (isset($_GET['review_error'])): ?>
            <div class="alert alert-danger">Please select a rating and write a review.</div>
        <?php endif; ?>

        <div class="row g-5 align-items-start">
            <div class="col-lg-6">
                <img class="img-fluid rounded-3 shadow-sm w-100" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-lg-6">
                <span class="badge badge-glam mb-3"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 class="section-title mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price fs-3 mb-2">RM <?php echo number_format($product['price'], 2); ?></p>
                <?php if ($reviews): ?>
                    <p class="text-muted mb-3"><i class="bi bi-star-fill text-warning me-1"></i><?php echo number_format($averageRating, 1); ?> average rating from <?php echo count($reviews); ?> review(s)</p>
                <?php endif; ?>
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
                    <div class="d-flex flex-wrap gap-3 align-items-end">
                        <form method="post" action="add_to_cart.php" class="d-flex flex-wrap gap-3 align-items-end">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div>
                                <label class="form-label fw-semibold">Quantity</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" style="width: 110px;">
                            </div>
                            <button type="submit" class="btn btn-berry btn-lg"><i class="bi bi-bag-plus me-2"></i>Add to Cart</button>
                        </form>
                        <form method="post" action="add_to_wishlist.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-outline-berry btn-lg"><i class="bi bi-heart me-2"></i>Wishlist</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Please <a href="login.php">login</a> to access cart, wishlist, and review features.
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="products.php" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>Back to products
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-5">
            <div class="col-lg-5">
                <div class="soft-panel p-4 h-100">
                    <h2 class="h4 fw-bold mb-3">Write a Review</h2>
                    <?php if (isLoggedIn()): ?>
                        <form method="post" action="add_review.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">Select rating</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Good</option>
                                    <option value="3">3 - Average</option>
                                    <option value="2">2 - Poor</option>
                                    <option value="1">1 - Bad</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Review</label>
                                <textarea name="comment" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-berry w-100">Submit Review</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted mb-0">Login as a customer to rate and review this product.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="soft-panel p-4 h-100">
                    <h2 class="h4 fw-bold mb-3">Customer Reviews</h2>
                    <?php if (!$reviews): ?>
                        <p class="text-muted mb-0">No reviews yet.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-bottom py-3">
                                <div class="d-flex justify-content-between gap-3">
                                    <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                    <span class="badge badge-glam"><?php echo $review['rating']; ?>/5</span>
                                </div>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($review['comment']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
