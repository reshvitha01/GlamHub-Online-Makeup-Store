<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$pageTitle = 'Login';
$error = '';
$identifier = '';
$role = $_POST['role'] ?? ($_GET['role'] ?? 'customer');
$success = isset($_GET['registered']) ? 'Registration successful. Please login to continue.' : '';
$restricted = isset($_GET['restricted']) ? 'Please login with an authorized staff or admin account.' : '';

if (!in_array($role, ['customer', 'staff', 'admin'], true)) {
    $role = 'customer';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($role === 'customer') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE role = ? AND (email = ? OR username = ?)');
        $stmt->execute(['customer', $identifier, $identifier]);
    } elseif ($role === 'staff') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE role = ? AND staff_admin_id = ?');
        $stmt->execute(['staff', strtoupper($identifier)]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE role = ? AND staff_admin_id = ?');
        $stmt->execute(['admin', strtoupper($identifier)]);
    }

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'] ?: $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } elseif ($user['role'] === 'staff') {
            header('Location: staff.php');
        } else {
            header('Location: products.php');
        }
        exit;
    }

    $error = 'Invalid login details. Please check your account type and password.';
}

$portalTitles = [
    'customer' => 'Customer Portal',
    'staff' => 'Staff Portal',
    'admin' => 'Admin Portal',
];

require_once 'includes/header.php';
?>

<section class="container auth-wrap py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="text-center mb-4">
                <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Secure Access</p>
                <h1 class="section-title mb-2">Login to GlamHub</h1>
                <p class="text-muted mb-0">Select the correct portal before entering your login details.</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($restricted): ?>
                <div class="alert alert-warning"><?php echo htmlspecialchars($restricted); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="row g-3 mb-4" id="portalCards">
                <div class="col-md-4">
                    <button type="button" class="portal-card soft-panel w-100 text-start <?php echo $role === 'customer' ? 'active' : ''; ?>" data-role="customer">
                        <span class="feature-icon mb-3"><i class="bi bi-person-heart"></i></span>
                        <span class="d-block h5 fw-bold mb-1">Customer</span>
                        <span class="d-block text-muted small">Login with email or username.</span>
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="portal-card soft-panel w-100 text-start <?php echo $role === 'staff' ? 'active' : ''; ?>" data-role="staff">
                        <span class="feature-icon mb-3"><i class="bi bi-person-badge"></i></span>
                        <span class="d-block h5 fw-bold mb-1">Staff</span>
                        <span class="d-block text-muted small">Login with official staff ID.</span>
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="portal-card soft-panel w-100 text-start <?php echo $role === 'admin' ? 'active' : ''; ?>" data-role="admin">
                        <span class="feature-icon mb-3"><i class="bi bi-shield-lock"></i></span>
                        <span class="d-block h5 fw-bold mb-1">Admin</span>
                        <span class="d-block text-muted small">Login with admin ID only.</span>
                    </button>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="soft-panel p-4 p-md-5">
                        <h2 class="h4 fw-bold mb-1" id="portalTitle"><?php echo htmlspecialchars($portalTitles[$role]); ?></h2>
                        <p class="text-muted mb-4" id="portalDescription">Enter your customer login details.</p>

                        <form method="post">
                            <input type="hidden" name="role" id="roleInput" value="<?php echo htmlspecialchars($role); ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" id="identifierLabel">Email or Username</label>
                                <input type="text" name="identifier" class="form-control" value="<?php echo htmlspecialchars($identifier); ?>" required>
                                <div class="form-text" id="identifierHelp">Example: sarah01 or sarah@gmail.com</div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <a href="forgot_password.php" class="small text-decoration-none" id="forgotLink">Forgot password?</a>
                                <span class="small text-muted" id="secureNote">Customer account</span>
                            </div>
                            <button type="submit" class="btn btn-berry w-100 py-2" id="loginButton">Login as Customer</button>
                        </form>

                        <p class="text-center mt-4 mb-0" id="registerText">New to GlamHub? <a href="register.php">Create beauty account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const roleInput = document.getElementById('roleInput');
const identifierLabel = document.getElementById('identifierLabel');
const identifierHelp = document.getElementById('identifierHelp');
const registerText = document.getElementById('registerText');
const portalTitle = document.getElementById('portalTitle');
const portalDescription = document.getElementById('portalDescription');
const loginButton = document.getElementById('loginButton');
const secureNote = document.getElementById('secureNote');
const forgotLink = document.getElementById('forgotLink');
const portalCards = document.querySelectorAll('.portal-card');

const portalContent = {
    customer: {
        title: 'Customer Portal',
        description: 'Access your beauty profile, recommendations, cart, and orders.',
        label: 'Email or Username',
        help: 'Example: sarah01 or sarah@gmail.com',
        button: 'Login as Customer',
        note: 'Customer account'
    },
    staff: {
        title: 'Staff Portal',
        description: 'For GlamHub staff to manage customer orders and support tasks.',
        label: 'Staff ID',
        help: 'Example: STF001',
        button: 'Login as Staff',
        note: 'Official staff access'
    },
    admin: {
        title: 'Admin Portal',
        description: 'For authorized administrators to manage products, staff, and system records.',
        label: 'Admin ID',
        help: 'Example: ADM001',
        button: 'Login as Admin',
        note: 'Restricted admin access'
    }
};

function applyRole(role) {
    const content = portalContent[role] || portalContent.customer;
    roleInput.value = role;
    portalTitle.textContent = content.title;
    portalDescription.textContent = content.description;
    identifierLabel.textContent = content.label;
    identifierHelp.textContent = content.help;
    loginButton.textContent = content.button;
    secureNote.textContent = content.note;
    registerText.style.display = role === 'customer' ? 'block' : 'none';
    forgotLink.textContent = role === 'customer' ? 'Forgot password?' : 'Ask admin to reset password';
    forgotLink.href = role === 'customer' ? 'forgot_password.php' : 'login.php';

    portalCards.forEach((card) => {
        card.classList.toggle('active', card.dataset.role === role);
    });
}

portalCards.forEach((card) => card.addEventListener('click', () => applyRole(card.dataset.role)));
applyRole(roleInput.value);
</script>

<?php require_once 'includes/footer.php'; ?>
