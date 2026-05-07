<?php
$pageTitle = 'Personalized Makeup Recommendation System';
require_once 'includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <p class="hero-kicker mb-3">GlamHub Cosmetics</p>
                <h1 class="hero-title mb-4">Personalized Makeup Recommendation System</h1>
                <p class="hero-text mb-4">
                    Explore makeup products by skin tone, undertone, and category.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="products.php" class="btn btn-light btn-lg fw-bold">
                        <i class="bi bi-bag-heart me-2"></i>Browse Products
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-outline-light btn-lg fw-bold">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-person-check"></i></span>
                <h3 class="h5 fw-bold">User Access</h3>
                <p class="text-muted mb-0">Customers can register, login, and safely access the website.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-grid"></i></span>
                <h3 class="h5 fw-bold">Product Browsing</h3>
                <p class="text-muted mb-0">Guests and customers can view makeup products before purchasing.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100">
                <span class="feature-icon mb-3"><i class="bi bi-funnel"></i></span>
                <h3 class="h5 fw-bold">Smart Filters</h3>
                <p class="text-muted mb-0">Products can be filtered by category, skin tone, and undertone.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
