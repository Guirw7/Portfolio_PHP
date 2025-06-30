<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Portfolios publics';

$stmt = $pdo->query("SELECT id, first_name, last_name, profile_image FROM users ORDER BY last_name ASC");
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container portfolios-list">
    <h1>Portfolios des Utilisateurs</h1>

    <input type="text" id="search" placeholder="Rechercher par nom..." class="portfolio-search mb-3">

    <div class="portfolios-grid" id="portfolioGrid">
        <?php foreach ($users as $user): ?>
            <div class="portfolio-user-card">
                <div class="user-avatar">
                    <img src="uploads/<?= $user['profile_image'] ?? 'default.png' ?>" alt="avatar">
                </div>
                <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                <a href="portfolio.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Voir le portfolio</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.getElementById("search").addEventListener("input", function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll(".portfolio-user-card").forEach(card => {
            const name = card.querySelector("h3").textContent.toLowerCase();
            card.style.display = name.includes(term) ? "block" : "none";
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
