<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Recommendation History';
$stmt = $pdo->prepare('SELECT recommendation_history.*, categories.name AS category_name
                       FROM recommendation_history
                       LEFT JOIN categories ON recommendation_history.category_id = categories.id
                       WHERE recommendation_history.user_id = ?
                       ORDER BY recommendation_history.created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <p class="hero-kicker mb-2" style="color: var(--glam-rose);">Smart Shade Finder</p>
            <h1 class="section-title mb-0">Recommendation History</h1>
        </div>
        <a href="shade_finder.php" class="btn btn-berry align-self-md-end">Find New Shade</a>
    </div>

    <?php if (!$items): ?>
        <div class="soft-panel p-5 text-center">
            <span class="feature-icon mb-3 mx-auto"><i class="bi bi-clock-history"></i></span>
            <h2 class="h4 fw-bold">No recommendation history yet</h2>
            <p class="text-muted">Use Smart Shade Finder to save your searches automatically.</p>
        </div>
    <?php else: ?>
        <div class="soft-panel p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Skin Tone</th>
                            <th>Undertone</th>
                            <th>Category</th>
                            <th>Results</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo date('d M Y, h:i A', strtotime($item['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($item['skin_tone']); ?></td>
                                <td><?php echo htmlspecialchars($item['undertone']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name'] ?? 'All Categories'); ?></td>
                                <td><span class="badge badge-glam"><?php echo $item['result_count']; ?> products</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
