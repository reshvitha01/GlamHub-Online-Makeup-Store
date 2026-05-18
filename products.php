<?php
require_once 'includes/db.php';

$pageTitle = 'Products';
$category = $_GET['category'] ?? '';
$skinTone = $_GET['skin_tone'] ?? '';
$undertone = $_GET['undertone'] ?? '';

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

$sql = 'SELECT products.*, categories.name AS category_name
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        WHERE 1 = 1';
$params = [];

if ($category !== '') {
    $sql .= ' AND categories.id = ?';
    $params[] = $category;
}

if ($skinTone !== '') {
    $sql .= ' AND products.skin_tone = ?';
    $params[] = $skinTone;
}

if ($undertone !== '') {
    $sql .= ' AND products.undertone = ?';
    $params[] = $undertone;
}

$sql .= ' ORDER BY products.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">GlamHub Collection</p>
            <h1 class="section-title mb-2">Browse Makeup Products</h1>
            <p class="text-muted mb-0">Explore cosmetics by category, skin tone, and undertone with a clean beauty shopping layout.</p>
        </div>
        <?php if (!isLoggedIn()): ?>
            <div class="align-self-lg-end">
                <a href="login.php" class="btn btn-outline-berry">
                    <i class="bi bi-lock me-2"></i>Login for Member Access
                </a>
            </div>
        <?php endif; ?>
    </div>

    <form class="soft-panel filter-panel p-3 p-md-4 mb-4" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $item): ?>
                        <option value="<?php echo $item['id']; ?>" <?php echo $category == $item['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Skin Tone</label>
                <select name="skin_tone" class="form-select">
                    <option value="">All Skin Tones</option>
                    <?php foreach (['Fair', 'Medium', 'Tan', 'Deep'] as $tone): ?>
                        <option value="<?php echo $tone; ?>" <?php echo $skinTone === $tone ? 'selected' : ''; ?>>
                            <?php echo $tone; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Undertone</label>
                <select name="undertone" class="form-select">
                    <option value="">All Undertones</option>
                    <?php foreach (['Cool', 'Warm', 'Neutral'] as $tone): ?>
                        <option value="<?php echo $tone; ?>" <?php echo $undertone === $tone ? 'selected' : ''; ?>>
                            <?php echo $tone; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-berry">
                    <i class="bi bi-funnel me-2"></i>Filter Products
                </button>
            </div>
        </div>
        <div class="mt-3">
            <a href="products.php" class="small text-decoration-none">Clear filters</a>
        </div>
    </form>

    <?php if (!$products): ?>
        <div class="alert alert-warning">No products found. Try a different filter.</div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-sm-6 col-lg-4">
                <article class="product-card">
                    <img class="product-image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="p-4">
                        <div class="d-flex justify-content-between gap-2 mb-2">
                            <span class="badge badge-glam"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <span class="price">RM <?php echo number_format($product['price'], 2); ?></span>
                        </div>
                        <h2 class="h5 fw-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h2>
                        <p class="text-muted mb-3">
                            <?php echo htmlspecialchars($product['shade']); ?> &middot; <?php echo htmlspecialchars($product['skin_tone']); ?> &middot; <?php echo htmlspecialchars($product['undertone']); ?>
                        </p>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-berry w-100">
                            View Details
                        </a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>


