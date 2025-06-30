<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Portfolio Public';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('index.php');
}

$user_id = (int)$_GET['id'];

// Récupérer infos utilisateur
$stmt = $pdo->prepare("SELECT first_name, last_name, bio, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) redirect('index.php');

// Pagination des projets
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_projects = $total_stmt->fetchColumn();
$total_pages = ceil($total_projects / $limit);

// Récupérer projets
$stmt = $pdo->prepare("SELECT title, description, image, link FROM projects WHERE user_id = ? ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();

// Récupérer compétences
$stmt = $pdo->prepare("
    SELECT s.name, us.level
    FROM user_skills us
    JOIN skills s ON us.skill_id = s.id
    WHERE us.user_id = ?
    ORDER BY us.level DESC
");
$stmt->execute([$user_id]);
$skills = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container portfolios-public">
    <div class="portfolio-banner">
        <img src="uploads/<?= $user['profile_image'] ?? 'default.png' ?>" class="avatar-lg" alt="Avatar">
        <div>
            <h1>Portfolio de <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
            <?php if (!empty($user['bio'])): ?>
                <p class="text-muted"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <section class="portfolio-section">
        <h2><i class="fas fa-cogs"></i> Compétences</h2>
        <div class="skills-badges">
            <?php foreach ($skills as $skill): ?>
                <span class="badge level-<?= $skill['level'] ?>">
                    <?= htmlspecialchars($skill['name']) ?> (<?= $skill['level'] ?>)
                </span>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="portfolio-section">
        <h2><i class="fas fa-project-diagram"></i> Projets</h2>
        <div class="portfolio-grid">
            <?php foreach ($projects as $project):
                $title = htmlspecialchars($project['title']);
                $desc = nl2br(htmlspecialchars($project['description']));
                $link = htmlspecialchars($project['link']);
                $image = !empty($project['image']) ? 'uploads/' . htmlspecialchars($project['image']) : '';
                $has_link = !empty($link);
                $has_image = !empty($project['image']);
                ?>
                <div class="portfolio-card"
                     data-title="<?= $title ?>"
                     data-description="<?= $desc ?>"
                     data-image="<?= $has_image ? $image : '' ?>"
                     data-link="<?= $has_link ? $link : '#' ?>">

                    <?php if ($has_image): ?>
                        <img src="<?= $image ?>" alt="image projet">
                    <?php endif; ?>

                    <div class="card-body">
                        <h5><?= $title ?></h5>
                        <p><?= substr(strip_tags($desc), 0, 100) ?>...</p>

                        <?php if ($has_link): ?>
                            <a href="<?= $link ?>" target="_blank" class="voir-plus-btn">Voir plus</a>
                        <?php else: ?>
                            <span class="text-muted" style="font-size: 0.85rem;">Aucun lien disponible</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?id=<?= $user_id ?>&page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="project-modal-overlay" id="projectModal">
    <div class="project-modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <img id="modalImage" src="" alt="">
        <h2 id="modalTitle"></h2>
        <p id="modalDescription"></p>
        <a id="modalLink" href="#" target="_blank" class="modal-link">Voir le projet complet</a>
        <span id="modalNoLink" class="modal-no-link" style="display: none;">Aucun lien disponible</span>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
