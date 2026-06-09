<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'GlamHub Cosmetics';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?> | GlamHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand glam-logo" href="index.php">
            <span class="logo-mark">G</span> GlamHub
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="shade_finder.php">Shade Finder</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>

                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
                    <li class="nav-item"><a class="nav-link" href="recommendation_history.php">History</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>

                    <?php if (hasRole(['staff', 'admin'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Management
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="staff.php"><i class="bi bi-clipboard-check me-2"></i>Staff Orders</a></li>
                                <?php if (hasRole('admin')): ?>
                                    <li><a class="dropdown-item" href="admin.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                    <li><a class="dropdown-item" href="admin_products.php"><i class="bi bi-box-seam me-2"></i>Manage Products</a></li>
                                    <li><a class="dropdown-item" href="admin_orders.php"><i class="bi bi-receipt me-2"></i>All Orders</a></li>
                                    <li><a class="dropdown-item" href="manage_accounts.php"><i class="bi bi-person-gear me-2"></i>Manage Accounts</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <span class="nav-link text-muted">Hi, <?php echo htmlspecialchars(currentUserName()); ?></span>
                    </li>
                    <li class="nav-item"><a class="btn btn-outline-berry btn-sm" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-berry btn-sm" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main>
