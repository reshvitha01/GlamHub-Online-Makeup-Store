<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Wishlist';
$stmt = $pdo->prepare('SELECT wishlist.id AS wishlist_id, products.*, categories.name AS category_name
                       FROM wishlist
                       INNER JOIN products ON wishlist.product_id = products.id
                       INNER JOIN categories ON products.category_id = categories.id
                       WHERE wishlist.user_id = ?
                       ORDER BY wishlist.created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Saved Beauty Picks</p>
            <h1 class="section-title mb-0">My Wishlist</h1>
        </div>
        <a href="products.php" class="btn btn-outline-berry align-self-md-end">Browse Products</a>
    </div>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Product saved to wishlist.</div>
    <?php endif; ?>
    <?php if (isset($_GET['removed'])): ?>
        <div class="alert alert-success">Product removed from wishlist.</div>
    <?php endif; ?>

    <?php if (!$items): ?>
        <div class="soft-panel p-5 text-center">
            <span class="feature-icon mb-3 mx-auto"><i class="bi bi-heart"></i></span>
            <h2 class="h4 fw-bold">No wishlist items yet</h2>
            <p class="text-muted">Save products you love and view them later.</p>
            <a href="products.php" class="btn btn-berry">Explore Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($items as $item): ?>
                <div class="col-sm-6 col-lg-4">
                    <article class="product-card">
                        <img class="product-image" src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="p-4">
                            <span class="badge badge-glam mb-2"><?php echo htmlspecialchars($item['category_name']); ?></span>
                            <h2 class="h5 fw-bold mb-2"><?php echo htmlspecialchars($item['name']); ?></h2>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($item['shade']); ?></p>
                            <p class="price">RM <?php echo number_format($item['price'], 2); ?></p>
                            <div class="d-flex gap-2">
                                <a href="product_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-berry flex-fill">Details</a>
                                <a href="remove_wishlist.php?id=<?php echo $item['wishlist_id']; ?>" class="btn btn-outline-danger">Remove</a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
