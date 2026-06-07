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
    $deliveryName    = trim($_POST['delivery_name'] ?? '');
    $deliveryPhone   = trim($_POST['delivery_phone'] ?? '');
    $deliveryAddress = trim($_POST['delivery_address'] ?? '');
    $paymentMethod   = trim($_POST['payment_method'] ?? '');

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
    if (!in_array($paymentMethod, ['cod', 'bank_transfer'])) {
        $errors[] = 'Please select a payment method.';
    }

    // Bank transfer receipt upload
    $receiptPath = null;
    if ($paymentMethod === 'bank_transfer') {
        if (empty($_FILES['receipt']['tmp_name'])) {
            $errors[] = 'Please upload your payment receipt.';
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
            $fileType = mime_content_type($_FILES['receipt']['tmp_name']);
            $fileSize = $_FILES['receipt']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = 'Receipt must be a JPG, PNG, WEBP, or PDF file.';
            } elseif ($fileSize > 5 * 1024 * 1024) {
                $errors[] = 'Receipt file must be under 5MB.';
            } else {
                $uploadDir = 'uploads/receipts/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
                $fileName = 'receipt_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                $receiptPath = $uploadDir . $fileName;
                if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $receiptPath)) {
                    $errors[] = 'Failed to upload receipt. Please try again.';
                    $receiptPath = null;
                }
            }
        }
    }

    if (!$errors) {
        $pdo->beginTransaction();

        $orderStatus = $paymentMethod === 'cod' ? 'pending' : 'awaiting_confirmation';

        $order = $pdo->prepare('INSERT INTO orders (user_id, total_amount, delivery_name, delivery_phone, delivery_address, payment_method, receipt_path, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $order->execute([
            $_SESSION['user_id'],
            $total,
            $deliveryName,
            $deliveryPhone,
            $deliveryAddress,
            $paymentMethod,
            $receiptPath,
            $orderStatus,
        ]);
        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, shade, price, quantity)
                                   VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($items as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['shade'], $item['price'], $item['quantity']]);
        }

        $clear = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
        $clear->execute([$_SESSION['user_id']]);

        $pdo->commit();

        header('Location: orders.php?placed=1&method=' . $paymentMethod);
        exit;
    }
}

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="text-center mb-4">
        <span class="hero-kicker">Almost there</span>
        <h1 class="section-title mt-1">Checkout</h1>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger" style="max-width: 860px; margin: 0 auto 1.5rem;">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!$items): ?>
        <div class="alert alert-warning text-center">Your cart is empty. <a href="products.php">Browse products</a>.</div>
    <?php else: ?>

    <form method="post" enctype="multipart/form-data">
        <div class="row g-4" style="max-width: 960px; margin: 0 auto;">

            <!-- LEFT: Delivery + Payment -->
            <div class="col-lg-7">

                <!-- Delivery Details -->
                <div class="soft-panel p-4 mb-4">
                    <h2 class="h5 fw-bold mb-3" style="color: var(--glam-berry);">
                        <i class="bi bi-geo-alt me-2"></i>Delivery Details
                    </h2>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Delivery Name</label>
                        <input type="text" name="delivery_name" class="form-control"
                               value="<?php echo htmlspecialchars(currentUserName()); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="delivery_phone" class="form-control"
                               inputmode="numeric" pattern="[0-9]{10,12}" maxlength="12"
                               title="Enter numbers only, 10 to 12 digits" data-phone-field required>
                        <div class="text-danger small phone-error-message mt-1"></div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <textarea name="delivery_address" class="form-control" rows="3" required></textarea>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="soft-panel p-4">
                    <h2 class="h5 fw-bold mb-3" style="color: var(--glam-berry);">
                        <i class="bi bi-credit-card me-2"></i>Payment Method
                    </h2>

                    <!-- COD Option -->
                    <label class="portal-card soft-panel d-flex align-items-center gap-3 mb-3 cursor-pointer" style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="cod" class="d-none payment-radio" required>
                        <div class="feature-icon flex-shrink-0">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="color: var(--glam-ink);">Cash on Delivery</div>
                            <div style="color: var(--glam-muted); font-size: .875rem;">Pay when your order arrives at your door</div>
                        </div>
                        <i class="bi bi-circle payment-check" style="font-size: 1.3rem; color: var(--glam-line);"></i>
                    </label>

                    <!-- Bank Transfer Option -->
                    <label class="portal-card soft-panel d-flex align-items-center gap-3" style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="bank_transfer" class="d-none payment-radio">
                        <div class="feature-icon flex-shrink-0">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="color: var(--glam-ink);">Bank Transfer</div>
                            <div style="color: var(--glam-muted); font-size: .875rem;">Transfer to our account and upload receipt</div>
                        </div>
                        <i class="bi bi-circle payment-check" style="font-size: 1.3rem; color: var(--glam-line);"></i>
                    </label>

                    <!-- Bank Transfer Details (shown when selected) -->
                    <div id="bank-details" class="mt-3 p-3 rounded-3 d-none" style="background: var(--glam-blush); border: 1px solid var(--glam-line);">
                        <p class="fw-semibold mb-2" style="color: var(--glam-berry);">Transfer to:</p>
                        <div style="font-size: .9rem; color: var(--glam-ink); line-height: 2;">
                            <div><strong>Bank:</strong> Maybank</div>
                            <div><strong>Account Name:</strong> GlamHub Sdn Bhd</div>
                            <div><strong>Account Number:</strong> 1234 5678 9012</div>
                        </div>
                        <hr style="border-color: var(--glam-line);">
                        <label class="form-label fw-semibold">Upload Payment Receipt</label>
                        <input type="file" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <div class="text-muted small mt-1">JPG, PNG, WEBP or PDF — max 5MB</div>
                    </div>
                </div>

            </div>

            <!-- RIGHT: Order Summary -->
            <div class="col-lg-5">
                <div class="soft-panel p-4" style="position: sticky; top: 100px;">
                    <h2 class="h5 fw-bold mb-3" style="color: var(--glam-berry);">
                        <i class="bi bi-bag-heart me-2"></i>Order Summary
                    </h2>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--glam-line);">
                            <div>
                                <div style="font-size: .92rem; font-weight: 600; color: var(--glam-ink);">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </div>
                                <?php if ($item['shade']): ?>
                                    <div style="font-size: .8rem; color: var(--glam-muted);"><?php echo htmlspecialchars($item['shade']); ?></div>
                                <?php endif; ?>
                                <div style="font-size: .82rem; color: var(--glam-muted);">Qty: <?php echo $item['quantity']; ?></div>
                            </div>
                            <strong class="price">RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between align-items-center pt-3 mb-4">
                        <span class="fw-semibold fs-5">Total</span>
                        <strong class="price fs-5">RM <?php echo number_format($total, 2); ?></strong>
                    </div>

                    <button type="submit" class="btn btn-berry w-100 py-2">
                        <i class="bi bi-lock me-2"></i>Place Order
                    </button>

                    <div class="text-center mt-3" style="color: var(--glam-muted); font-size: .8rem;">
                        <i class="bi bi-shield-check me-1" style="color: var(--glam-berry);"></i>
                        Your information is safe and secure
                    </div>
                </div>
            </div>

        </div>
    </form>

    <?php endif; ?>
</section>

<script>
// Payment method selection styling
document.querySelectorAll('.payment-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.portal-card').forEach(card => {
            card.classList.remove('active');
            card.querySelector('.payment-check').className = 'bi bi-circle payment-check';
            card.querySelector('.payment-check').style.color = 'var(--glam-line)';
        });
        const card = this.closest('.portal-card');
        card.classList.add('active');
        const check = card.querySelector('.payment-check');
        check.className = 'bi bi-check-circle-fill payment-check';
        check.style.color = 'var(--glam-berry)';

        // Show/hide bank details
        const bankDetails = document.getElementById('bank-details');
        if (this.value === 'bank_transfer') {
            bankDetails.classList.remove('d-none');
        } else {
            bankDetails.classList.add('d-none');
        }
    });
});

// Phone validation
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

document.querySelectorAll('input[data-phone-field]').forEach(input => {
    input.addEventListener('input', () => validatePhoneInput(input));
    input.addEventListener('blur', () => validatePhoneInput(input));
});
</script>

<?php require_once 'includes/footer.php'; ?>