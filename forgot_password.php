<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$pageTitle = 'Forgot Password';
$errors = [];
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid registered email address.';
    }

    if (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND role = 'customer'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $errors[] = 'No customer account found with this email address.';
        } else {
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ? AND role = 'customer'");
            $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $email]);
            $success = 'Password reset successful. You can login with your new password.';
            $email = '';
        }
    }
}

require_once 'includes/header.php';
?>

<section class="container auth-wrap d-flex align-items-center py-5">
    <div class="row justify-content-center w-100">
        <div class="col-lg-5">
            <div class="soft-panel p-4 p-md-5">
                <h1 class="section-title h2 mb-2">Reset Password</h1>
                <p class="text-muted mb-4">Customers can reset password using their registered email. Staff and admin must contact the admin for password reset.</p>

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

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Registered Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-berry w-100 py-2">Reset Password</button>
                </form>

                <p class="text-center mt-4 mb-0">Remember your password? <a href="login.php">Back to login</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
