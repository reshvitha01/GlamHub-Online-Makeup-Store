<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Checkout';
$errors = [];
$stmt = $pdo->prepare('SELECT cart_items.quantity, products.*
                       FROM cart_items
                       INNER JOIN products ON cart_items.product_id = products.id
                       WHERE cart_items.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryName = trim($_POST['delivery_name'] ?? '');
    $deliveryPhone = trim($_POST['delivery_phone'] ?? '');
    $deliveryAddress = trim($_POST['delivery_address'] ?? '');

    if (!$items) {
        $errors[] = 'Your cart is empty.';
    }
    if ($deliveryName === '') {
        $errors[] = 'Delivery name is required.';
    }
    if ($deliveryPhone === '') {
        $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9]{10,12}$/', $deliveryPhone)) {
        $errors[] = 'Invalid phone number. Please enter numbers only, 10 to 12 digits.';
    }
    if ($deliveryAddress === '') {
        $errors[] = 'Delivery address is required.';
    }

    if (!$errors) {
        $pdo->beginTransaction();
        $order = $pdo->prepare('INSERT INTO orders (user_id, total_amount, delivery_name, delivery_phone, delivery_address) VALUES (?, ?, ?, ?, ?)');
        $order->execute([$_SESSION['user_id'], $total, $deliveryName, $deliveryPhone, $deliveryAddress]);
        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, shade, price, quantity) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($items as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['shade'], $item['price'], $item['quantity']]);
        }

        $clear = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
        $clear->execute([$_SESSION['user_id']]);
        $pdo->commit();

        header('Location: orders.php?placed=1');
        exit;
    }
}

require_once 'includes/header.php';
?>

<section class="container py-5">
    <h1 class="section-title mb-4">Checkout</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!$items): ?>
        <div class="alert alert-warning">Your cart is empty. <a href="products.php">Browse products</a>.</div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <form class="soft-panel p-4" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Delivery Name</label>
                        <input type="text" name="delivery_name" class="form-control" value="<?php echo htmlspecialchars(currentUserName()); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="delivery_phone" class="form-control" inputmode="numeric" pattern="[0-9]{10,12}" maxlength="12" title="Enter numbers only, 10 to 12 digits" data-phone-field required><div class="text-danger small phone-error-message mt-1"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <textarea name="delivery_address" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-berry w-100 py-2">Place Order</button>
                </form>
            </div>

            <div class="col-lg-5">
                <div class="soft-panel p-4">
                    <h2 class="h4 fw-bold mb-3">Summary</h2>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                            <strong>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between pt-3 fs-5">
                        <span>Total</span>
                        <strong>RM <?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>


<script>
function validatePhoneInput(input) {
    const message = input.parentElement.querySelector('.phone-error-message');
    const isValid = /^[0-9]{10,12}$/.test(input.value.trim());

    if (input.value.trim() === '') {
        input.setCustomValidity('Phone number is required.');
        if (message) message.textContent = '';
        return;
    }

    if (!isValid) {
        input.setCustomValidity('Invalid phone number. Please enter numbers only, 10 to 12 digits.');
        if (message) message.textContent = 'Invalid phone number. Please enter numbers only, 10 to 12 digits.';
    } else {
        input.setCustomValidity('');
        if (message) message.textContent = '';
    }
}

document.querySelectorAll('input[data-phone-field]').forEach((input) => {
    input.addEventListener('input', () => validatePhoneInput(input));
    input.addEventListener('blur', () => validatePhoneInput(input));
});
</script>
<?php require_once 'includes/footer.php'; ?>




