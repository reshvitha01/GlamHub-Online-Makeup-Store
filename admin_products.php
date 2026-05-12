<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('admin');

$pageTitle = 'Admin Products';
$errors = [];
$success = '';

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$deleteId]);
    header('Location: admin_products.php?deleted=1');
    exit;
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $shade = trim($_POST['shade'] ?? '');
    $skinTone = trim($_POST['skin_tone'] ?? '');
    $undertone = trim($_POST['undertone'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $imageUrl = trim($_POST['image_url'] ?? '');

    if ($categoryId <= 0) {
        $errors[] = 'Category is required.';
    }
    if ($name === '') {
        $errors[] = 'Product name is required.';
    }
    if ($shade === '') {
        $errors[] = 'Shade is required.';
    }
    if ($skinTone === '') {
        $errors[] = 'Skin tone is required.';
    }
    if ($undertone === '') {
        $errors[] = 'Undertone is required.';
    }
    if ($description === '') {
        $errors[] = 'Description is required.';
    }
    if ($price <= 0) {
        $errors[] = 'Price must be more than 0.';
    }
    if ($imageUrl === '') {
        $errors[] = 'Image path is required. Example: assets/images/foundation.png';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO products (category_id, name, shade, skin_tone, undertone, description, price, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$categoryId, $name, $shade, $skinTone, $undertone, $description, $price, $imageUrl]);
        $success = 'Product added successfully.';
    }
}

$productStmt = $pdo->query('SELECT products.*, categories.name AS category_name
                           FROM products
                           INNER JOIN categories ON products.category_id = categories.id
                           ORDER BY products.created_at DESC');
$products = $productStmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="mb-4">
        <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Admin Panel</p>
        <h1 class="section-title mb-0">Product Management</h1>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Product deleted successfully.</div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <form class="soft-panel p-4" method="post">
                <h2 class="h4 fw-bold mb-3">Add New Product</h2>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Shade</label>
                    <input type="text" name="shade" class="form-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Skin Tone</label>
                        <select name="skin_tone" class="form-select" required>
                            <option value="">Select</option>
                            <?php foreach (['Fair', 'Medium', 'Tan', 'Deep'] as $tone): ?>
                                <option value="<?php echo $tone; ?>"><?php echo $tone; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Undertone</label>
                        <select name="undertone" class="form-select" required>
                            <option value="">Select</option>
                            <?php foreach (['Cool', 'Warm', 'Neutral'] as $tone): ?>
                                <option value="<?php echo $tone; ?>"><?php echo $tone; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Image Path</label>
                    <input type="text" name="image_url" class="form-control" placeholder="assets/images/product.png" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-berry w-100">Add Product</button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="soft-panel p-4">
                <h2 class="h4 fw-bold mb-3">Product List</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($product['shade']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>RM <?php echo number_format($product['price'], 2); ?></td>
                                    <td class="text-end">
                                        <a href="admin_products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this product?">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

