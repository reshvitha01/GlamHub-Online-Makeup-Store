<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$pageTitle = 'Register';
$errors = [];
$fullName = '';
$username = '';
$email = '';
$phone = '';
$skinTone = '';
$skinType = '';
$makeupPreference = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $skinTone = trim($_POST['skin_tone'] ?? '');
    $skinType = trim($_POST['skin_type'] ?? '');
    $makeupPreference = trim($_POST['makeup_preference'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }

    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    }

    if ($skinTone === '') {
        $errors[] = 'Skin tone is required.';
    }

    if ($skinType === '') {
        $errors[] = 'Skin type is required.';
    }

    if ($makeupPreference === '') {
        $errors[] = 'Makeup preference is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    }

    if (!$errors) {
        $checkEmail = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $checkEmail->execute([$email]);

        if ($checkEmail->fetch()) {
            $errors[] = 'This email is already registered.';
        }

        $checkUsername = $pdo->prepare('SELECT id FROM users WHERE username = ? AND role = ?');
        $checkUsername->execute([$username, 'customer']);

        if ($checkUsername->fetch()) {
            $errors[] = 'This username is already registered.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users (username, full_name, email, phone, role, staff_admin_id, skin_tone, skin_type, makeup_preference, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$username, $fullName, $email, $phone, 'customer', null, $skinTone, $skinType, $makeupPreference, password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php?registered=1');
        exit;
    }
}

require_once 'includes/header.php';
?>

<section class="container auth-wrap d-flex align-items-center py-5">
    <div class="row justify-content-center w-100">
        <div class="col-lg-7">
            <div class="soft-panel p-4 p-md-5">
                <h1 class="section-title h2 mb-2">Create Beauty Account</h1>
                <p class="text-muted mb-4">Create your GlamHub customer profile for a more personalized makeup shopping experience.</p>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($fullName); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Skin Tone</label>
                            <select name="skin_tone" class="form-select" required>
                                <option value="">Select</option>
                                <?php foreach (['Fair', 'Medium', 'Tan', 'Deep'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo $skinTone === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Skin Type</label>
                            <select name="skin_type" class="form-select" required>
                                <option value="">Select</option>
                                <?php foreach (['Normal', 'Dry', 'Oily', 'Combination', 'Sensitive'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo $skinType === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Makeup Preference</label>
                            <select name="makeup_preference" class="form-select" required>
                                <option value="">Select</option>
                                <?php foreach (['Natural', 'Matte', 'Glowy', 'Bold', 'Long-lasting'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo $makeupPreference === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-berry w-100 py-2 mt-4">Create Beauty Account</button>
                </form>
                <p class="text-center mt-4 mb-0">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
