<?php
$pageTitle = 'Beauty Profile Ready';
require_once 'includes/header.php';
?>

<section class="success-glam-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="success-glam-card soft-panel p-4 p-md-5 text-center">
                    <div class="success-sparkle mx-auto mb-4">
                        <i class="bi bi-stars"></i>
                    </div>
                    <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Registration Complete</p>
                    <h1 class="section-title mb-3">Your Beauty Profile is Ready</h1>
                    <p class="lead mb-4">
                        We have created a personalized beauty experience for you.
                        Ready to discover your perfect look?
                    </p>

                    <div class="beauty-ready-list text-start mx-auto mb-4">
                        <div><i class="bi bi-check-circle-fill"></i> Beauty profile saved</div>
                        <div><i class="bi bi-check-circle-fill"></i> Personalized shade details prepared</div>
                        <div><i class="bi bi-check-circle-fill"></i> Smart recommendations ready to explore</div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="login.php" class="btn btn-outline-berry btn-lg">
                            Login First
                        </a>
                        <a href="shade_finder.php" class="btn btn-berry btn-lg">
                            <i class="bi bi-magic me-2"></i>Explore Recommendations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
