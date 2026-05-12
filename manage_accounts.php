<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireRole('admin');

$pageTitle = 'Manage Accounts';
$errors = [];
$success = '';
$allowedRoles = ['staff', 'admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_account') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'staff';
        $officialId = strtoupper(trim($_POST['staff_admin_id'] ?? ''));
        $department = trim($_POST['department'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($fullName === '') {
            $errors[] = 'Full name is required.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        if (!in_array($role, $allowedRoles, true)) {
            $errors[] = 'Invalid account role.';
        }

        if ($officialId === '') {
            $errors[] = 'Staff/Admin ID is required.';
        }

        if ($role === 'staff' && !preg_match('/^STF[0-9]{3}$/', $officialId)) {
            $errors[] = 'Staff ID must use format STF001.';
        }

        if ($role === 'admin' && !preg_match('/^ADM[0-9]{3}$/', $officialId)) {
            $errors[] = 'Admin ID must use format ADM001.';
        }

        if ($role === 'staff' && $department === '') {
            $errors[] = 'Department is required for staff.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (!$errors) {
            $checkId = $pdo->prepare('SELECT id FROM users WHERE staff_admin_id = ?');
            $checkId->execute([$officialId]);

            if ($checkId->fetch()) {
                $errors[] = 'This Staff/Admin ID already exists.';
            }

            $checkEmail = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $checkEmail->execute([$email]);

            if ($checkEmail->fetch()) {
                $errors[] = 'This email is already registered.';
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO users (username, full_name, email, role, staff_admin_id, department, password) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$officialId, $fullName, $email, $role, $officialId, $department, password_hash($password, PASSWORD_DEFAULT)]);
            $success = ucfirst($role) . ' account created successfully.';
        }
    }

    if ($action === 'reset_password') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (!$errors) {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? AND role IN ('staff', 'admin')");
            $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
            $success = 'Password reset successfully.';
        }
    }
}

$stmt = $pdo->query("SELECT id, full_name, email, role, staff_admin_id, department, created_at FROM users WHERE role IN ('staff', 'admin') ORDER BY role, staff_admin_id");
$accounts = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">Admin Control</p>
            <h1 class="section-title mb-0">Manage Staff & Admin Accounts</h1>
        </div>
        <a href="admin.php" class="btn btn-outline-berry align-self-lg-end">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
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

    <div class="row g-4">
        <div class="col-lg-5">
            <form class="soft-panel p-4" method="post">
                <input type="hidden" name="action" value="create_account">
                <h2 class="h4 fw-bold mb-3">Create Official Account</h2>
                <p class="text-muted">Staff and admin accounts are created only by admin for security.</p>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Staff/Admin ID</label>
                    <input type="text" name="staff_admin_id" class="form-control" placeholder="STF006 or ADM002" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Staff/Admin Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="Sales, Customer Support, Inventory">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Temporary Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-berry w-100">Create Official Account</button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="soft-panel p-4">
                <h2 class="h4 fw-bold mb-3">Official Account List</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>ID</th>
                                <th>Department</th>
                                <th>Password Reset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($account['full_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($account['email']); ?></div>
                                    </td>
                                    <td><span class="badge badge-glam"><?php echo htmlspecialchars(ucfirst($account['role'])); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($account['staff_admin_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($account['department'] ?: '-'); ?></td>
                                    <td>
                                        <form method="post" class="d-flex flex-column gap-2">
                                            <input type="hidden" name="action" value="reset_password">
                                            <input type="hidden" name="user_id" value="<?php echo $account['id']; ?>">
                                            <input type="password" name="new_password" class="form-control form-control-sm" placeholder="New password" required>
                                            <input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="Confirm password" required>
                                            <button type="submit" class="btn btn-sm btn-outline-berry">Reset</button>
                                        </form>
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
