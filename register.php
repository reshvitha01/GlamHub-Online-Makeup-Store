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

<section class="container auth-wrap py-5">
    <div class="row justify-content-center align-items-start g-4">
        <div class="col-lg-7">
            <div class="soft-panel p-4 p-md-5">
                <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Beauty Profile</p>
                <h1 class="section-title h2 mb-2">Create Beauty Account</h1>
                <p class="text-muted mb-4">Complete the form and watch the GlamHub face transform with makeup.</p>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post" id="beautyRegisterForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="full_name" class="form-control beauty-field" data-step="mascara" value="<?php echo htmlspecialchars($fullName); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control beauty-field" data-step="brows" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control beauty-field" data-step="eyeshadow" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control beauty-field" data-step="lips" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Skin Tone</label>
                            <select name="skin_tone" class="form-select beauty-field" data-step="foundation" required>
                                <option value="">Select</option>
                                <?php foreach (['Fair', 'Medium', 'Tan', 'Deep'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo $skinTone === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Skin Type</label>
                            <select name="skin_type" class="form-select beauty-field" data-step="blush" required>
                                <option value="">Select</option>
                                <?php foreach (['Normal', 'Dry', 'Oily', 'Combination', 'Sensitive'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo $skinType === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Makeup Preference</label>
                            <select name="makeup_preference" class="form-select beauty-field" data-step="highlight" required>
                                <option value="">Select</option>
                                <?php foreach (['Natural', 'Matte', 'Glowy', 'Bold', 'Long-lasting'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo $makeupPreference === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control beauty-field" data-step="smile" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control beauty-field" data-step="smile" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-berry w-100 py-2 mt-4">Create Beauty Account</button>
                </form>
                <p class="text-center mt-4 mb-0">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="beauty-preview soft-panel p-4 p-md-5 text-center sticky-top" style="top: 95px;">
                <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Live Form Preview</p>
                <h2 class="h4 fw-bold mb-3">Makeup Progress</h2>
                <div class="face-stage" id="faceStage"><div class="hair-shape"></div><div class="face-shape"><div class="foundation-layer"></div><div class="highlight left"></div><div class="highlight right"></div><div class="brow left"></div><div class="brow right"></div><div class="eyeshadow left"></div><div class="eyeshadow right"></div><div class="eye left"><span class="lash lash-1"></span><span class="lash lash-2"></span><span class="lash lash-3"></span></div><div class="eye right"><span class="lash lash-1"></span><span class="lash lash-2"></span><span class="lash lash-3"></span></div><div class="blush left"></div><div class="blush right"></div><div class="nose"></div><div class="mouth"></div><div class="lipstick"></div></div></div>
                <div class="progress mt-4" role="progressbar" aria-label="Registration progress">
                    <div class="progress-bar" id="beautyProgress" style="width: 0%"></div>
                </div>
                <p class="fw-bold mt-3 mb-1" id="faceMood">Face is waiting for your details.</p>
                <p class="text-muted mb-0" id="progressText">0 of 8 required parts completed.</p>
            </div>
        </div>
    </div>
</section>

<script>
const form = document.getElementById('beautyRegisterForm');
const faceStage = document.getElementById('faceStage');
const progressBar = document.getElementById('beautyProgress');
const progressText = document.getElementById('progressText');
const faceMood = document.getElementById('faceMood');
const fields = Array.from(document.querySelectorAll('.beauty-field'));

function isFilled(field) {
    return field.value.trim() !== '';
}

function updateBeautyFace() {
    const completed = fields.filter(isFilled).length;
    const total = fields.length;
    const percent = Math.round((completed / total) * 100);
    const steps = new Set(fields.filter(isFilled).map((field) => field.dataset.step));

    faceStage.classList.toggle('has-mascara', steps.has('mascara'));
    faceStage.classList.toggle('has-brows', steps.has('brows'));
    faceStage.classList.toggle('has-eyeshadow', steps.has('eyeshadow'));
    faceStage.classList.toggle('has-lips', steps.has('lips'));
    faceStage.classList.toggle('has-foundation', steps.has('foundation'));
    faceStage.classList.toggle('has-blush', steps.has('blush'));
    faceStage.classList.toggle('has-highlight', steps.has('highlight'));
    faceStage.classList.toggle('is-happy', completed === total);

    progressBar.style.width = percent + '%';
    progressText.textContent = completed + ' of ' + total + ' required parts completed.';
    faceMood.textContent = completed === total ? 'Full makeup complete. Face is happy.' : 'Plain face is waiting for makeup details.';
}

fields.forEach((field) => {
    field.addEventListener('input', updateBeautyFace);
    field.addEventListener('change', updateBeautyFace);
});

updateBeautyFace();
</script>

<?php require_once 'includes/footer.php'; ?>

