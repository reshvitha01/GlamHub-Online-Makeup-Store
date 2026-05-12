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
                    Discover shades that suit your skin tone, undertone, and beauty preferences through a softer, smarter makeup shopping experience.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="shade_finder.php" class="btn btn-light btn-lg fw-bold">
                        <i class="bi bi-stars me-2"></i>Find My Shade
                    </a>
                    <a href="products.php" class="btn btn-outline-light btn-lg fw-bold">
                        <i class="bi bi-bag-heart me-2"></i>Browse Products
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-berry btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Create Beauty Account
                        </a>
                    <?php endif; ?>
                </div>
                <div class="glass-strip d-flex flex-wrap gap-4 p-3">
                    <div class="hero-stat">
                        <strong>Smart</strong>
                        <span>Shade matching</span>
                    </div>
                    <div class="hero-stat">
                        <strong>Beauty</strong>
                        <span>Profile based</span>
                    </div>
                    <div class="hero-stat">
                        <strong>Easy</strong>
                        <span>Online shopping</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="text-center mb-4">
        <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Why GlamHub</p>
        <h2 class="section-title mb-2">A guided beauty experience for confident choices</h2>
        <p class="text-muted mb-0">Designed for users who want product suggestions that feel personal, clear, and easy to explore.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto"><i class="bi bi-person-check"></i></span>
                <h3 class="h5 fw-bold">Personal Profile</h3>
                <p class="text-muted mb-0">Customers can register with beauty details such as skin tone, skin type, and makeup preference.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto"><i class="bi bi-palette"></i></span>
                <h3 class="h5 fw-bold">Smart Shade Finder</h3>
                <p class="text-muted mb-0">The system recommends suitable products using simple rule-based matching from user input.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto"><i class="bi bi-bag-heart"></i></span>
                <h3 class="h5 fw-bold">Beauty Store</h3>
                <p class="text-muted mb-0">Users can browse, filter, view product details, add to cart, and place orders smoothly.</p>
            </div>
        </div>
    </div>
</section>

<section class="page-band py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Ready to glow</p>
                <h2 class="section-title mb-2">Start with your skin tone and undertone</h2>
                <p class="text-muted mb-0">The recommendation module helps customers narrow down suitable makeup products before purchasing.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <a href="shade_finder.php" class="btn btn-berry btn-lg">
                    <i class="bi bi-magic me-2"></i>Open Smart Shade Finder
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
