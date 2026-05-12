<?php
require_once 'includes/db.php';

$pageTitle = 'Smart Shade Finder';
$skinTone = $_GET['skin_tone'] ?? '';
$undertone = $_GET['undertone'] ?? '';
$category = $_GET['category'] ?? '';
$recommendations = [];
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

if ($skinTone !== '' || $undertone !== '' || $category !== '') {
    $sql = 'SELECT products.*, categories.name AS category_name
            FROM products
            INNER JOIN categories ON products.category_id = categories.id
            WHERE 1 = 1';
    $params = [];

    if ($skinTone !== '') {
        $sql .= ' AND products.skin_tone = ?';
        $params[] = $skinTone;
    }

    if ($undertone !== '') {
        $sql .= ' AND products.undertone = ?';
        $params[] = $undertone;
    }

    if ($category !== '') {
        $sql .= ' AND products.category_id = ?';
        $params[] = $category;
    }

    $sql .= ' ORDER BY products.name ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recommendations = $stmt->fetchAll();
}

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="row g-5 align-items-start">
        <div class="col-lg-5">
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Smart Shade Finder</p>
            <h1 class="section-title mb-3">Find makeup shades that suit you</h1>
            <p class="text-muted mb-4">Select your beauty profile details and receive suitable product suggestions using GlamHub rule-based matching.</p>

            <form class="soft-panel filter-panel p-4" method="get">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Skin Tone</label>
                    <select name="skin_tone" class="form-select" required>
                        <option value="">Select Skin Tone</option>
                        <?php foreach (['Fair', 'Medium', 'Tan', 'Deep'] as $tone): ?>
                            <option value="<?php echo $tone; ?>" <?php echo $skinTone === $tone ? 'selected' : ''; ?>><?php echo $tone; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Undertone</label>
                    <select name="undertone" class="form-select" required>
                        <option value="">Select Undertone</option>
                        <?php foreach (['Cool', 'Warm', 'Neutral'] as $tone): ?>
                            <option value="<?php echo $tone; ?>" <?php echo $undertone === $tone ? 'selected' : ''; ?>><?php echo $tone; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Product Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $item): ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo $category == $item['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($item['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-berry w-100 py-2">
                    <i class="bi bi-stars me-2"></i>Get Recommendations
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title h3 mb-0">Recommended Products</h2>
                <a href="shade_finder.php" class="small text-decoration-none">Reset</a>
            </div>

            <?php if ($skinTone === '' && $undertone === '' && $category === ''): ?>
                <div class="soft-panel p-4 text-center">
                    <span class="feature-icon mb-3 mx-auto"><i class="bi bi-magic"></i></span>
                    <p class="text-muted mb-0">Your personalized recommendations will appear here after you submit the form.</p>
                </div>
            <?php elseif (!$recommendations): ?>
                <div class="alert alert-warning">No exact match found. Try selecting only skin tone and undertone.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($recommendations as $product): ?>
                        <div class="col-md-6">
                            <article class="product-card">
                                <img class="product-image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="p-4">
                                    <span class="badge badge-glam mb-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                    <h3 class="h5 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($product['shade']); ?> · <?php echo htmlspecialchars($product['skin_tone']); ?> · <?php echo htmlspecialchars($product['undertone']); ?></p>
                                    <p class="price">RM <?php echo number_format($product['price'], 2); ?></p>
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-berry w-100">View Details</a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

