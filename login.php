<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$pageTitle = 'Login';
$error = '';
$email = '';
$success = isset($_GET['registered']) ? 'Registration successful. Please login to continue.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header('Location: products.php');
        exit;
    }

    $error = 'Invalid email or password.';
}

require_once 'includes/header.php';
?>

<section class="container auth-wrap d-flex align-items-center py-5">
    <div class="row justify-content-center w-100">
        <div class="col-lg-5">
            <div class="soft-panel p-4 p-md-5">
                <h1 class="section-title h2 mb-2">Welcome Back</h1>
                <p class="text-muted mb-4">Login to continue your GlamHub experience.</p>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="text-end mb-4">
                        <a href="forgot_password.php" class="small text-decoration-none">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-berry w-100 py-2">Login</button>
                </form>

                <p class="text-center mt-4 mb-0">New to GlamHub? <a href="register.php">Create account</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

