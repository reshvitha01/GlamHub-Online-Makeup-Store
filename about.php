<?php
$pageTitle = 'About';
require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">
                About GlamHub
            </p>
            <h1 class="section-title mb-4">
                Beauty shopping made easier with personalized makeup guidance.
            </h1>
            <p class="lead text-muted">
                GlamHub Cosmetics is a beauty-focused online platform designed to help users choose suitable makeup products based on their skin tone, undertone, and personal preferences.
            </p>
            <p class="text-muted">
                The system helps reduce wrong shade selection during online makeup shopping by giving users a more guided, attractive, and user-friendly product browsing experience.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="products.php" class="btn btn-berry">
                    <i class="bi bi-bag-heart me-2"></i>Browse Products
                </a>
                <a href="register.php" class="btn btn-outline-berry">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="soft-panel p-3">
                <img
                    src="assets/images/about-glamhub.png"
                    alt="GlamHub makeup products"
                    class="img-fluid rounded-3 w-100 about-image"
                >
            </div>
        </div>
    </div>
</section>

<section class="container pb-5">
    <div class="text-center mb-4">
        <p class="hero-kicker text-uppercase mb-2" style="color: var(--glam-rose);">
            Why GlamHub
        </p>
        <h2 class="section-title h1">Designed for confident beauty choices</h2>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto">
                    <i class="bi bi-palette"></i>
                </span>
                <h3 class="h5 fw-bold">Personalized Beauty</h3>
                <p class="text-muted mb-0">
                    Users can explore makeup products that match their skin tone, undertone, and beauty preferences.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto">
                    <i class="bi bi-bag-heart"></i>
                </span>
                <h3 class="h5 fw-bold">Online Shopping Support</h3>
                <p class="text-muted mb-0">
                    Product details and filters help customers make better decisions before buying makeup online.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="soft-panel p-4 h-100 text-center">
                <span class="feature-icon mb-3 mx-auto">
                    <i class="bi bi-stars"></i>
                </span>
                <h3 class="h5 fw-bold">Feminine Experience</h3>
                <p class="text-muted mb-0">
                    The website uses soft colors, clean spacing, and modern layouts suitable for a cosmetics brand.
                </p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
