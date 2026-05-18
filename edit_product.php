<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireRole('admin');

$pageTitle = 'Edit Product';
$id = (int)($_GET['id'] ?? 0);
$errors = [];
$success = '';
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    require_once 'includes/header.php';
    echo '<section class="container py-5"><div class="alert alert-danger">Product not found.</div><a class="btn btn-outline-berry" href="admin_products.php">Back</a></section>';
    require_once 'includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $shade = trim($_POST['shade'] ?? '');
    $skinTone = trim($_POST['skin_tone'] ?? '');
    $undertone = trim($_POST['undertone'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $imageUrl = trim($_POST['image_url'] ?? '');

    if ($categoryId <= 0 || $name === '' || $shade === '' || $skinTone === '' || $undertone === '' || $description === '' || $price <= 0 || $imageUrl === '') {
        $errors[] = 'Please complete all product fields correctly.';
    }

    if (!$errors) {
        $update = $pdo->prepare('UPDATE products SET category_id = ?, name = ?, shade = ?, skin_tone = ?, undertone = ?, description = ?, price = ?, image_url = ? WHERE id = ?');
        $update->execute([$categoryId, $name, $shade, $skinTone, $undertone, $description, $price, $imageUrl, $id]);
        header('Location: admin_products.php?updated=1');
        exit;
    }

    $product = array_merge($product, $_POST);
}

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="mb-4">
        <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Admin Panel</p>
        <h1 class="section-title mb-0">Edit Product</h1>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errors[0]); ?></div>
    <?php endif; ?>

    <form class="soft-panel p-4" method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Shade</label>
                <input type="text" name="shade" class="form-control" value="<?php echo htmlspecialchars($product['shade']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Skin Tone</label>
                <select name="skin_tone" class="form-select" required>
                    <?php foreach (['Fair', 'Medium', 'Tan', 'Deep'] as $tone): ?>
                        <option value="<?php echo $tone; ?>" <?php echo $product['skin_tone'] === $tone ? 'selected' : ''; ?>><?php echo $tone; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Undertone</label>
                <select name="undertone" class="form-select" required>
                    <?php foreach (['Cool', 'Warm', 'Neutral'] as $tone): ?>
                        <option value="<?php echo $tone; ?>" <?php echo $product['undertone'] === $tone ? 'selected' : ''; ?>><?php echo $tone; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Image Path</label>
                <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-berry">Save Changes</button>
            <a href="admin_products.php" class="btn btn-outline-berry">Cancel</a>
        </div>
    </form>
</section>

<?php require_once 'includes/footer.php'; ?>
