<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Mes Projets';
$user_id = $_SESSION['user_id'];

// Gestion tri
$sort = $_GET['sort'] ?? 'date_desc';
$order_by = match($sort) {
    'date_asc' => 'created_at ASC',
    'az' => 'title ASC',
    'za' => 'title DESC',
    default => 'created_at DESC',
};

// Pagination
$per_page = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total = $total_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// Récupération des projets
$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY $order_by LIMIT $per_page OFFSET $offset");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();

$view = $_GET['view'] ?? 'cards';

include 'includes/header.php';
?>

<div class="container mt-5 manage-projects-wrapper">
    <div class="manage-section">

        <div class="manage-header">
            <h2><i class="fas fa-folder-open"></i> Mes Projets</h2>
            <div class="manage-actions-group">
                <a href="add-project.php" class="btn btn-sm2 btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter un projet
                </a>

                <form method="GET" class="manage-toolbar" id="filterForm">
                    <input type="hidden" name="view" id="viewInput" value="<?= $view ?>">
                    <select name="sort" class="btn btn-sm2 btn-outline" onchange="document.getElementById('filterForm').submit()">
                        <option value="az" <?= $sort === 'az' ? 'selected' : '' ?>>A → Z</option>
                        <option value="za" <?= $sort === 'za' ? 'selected' : '' ?>>Z → A</option>
                        <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Plus récent</option>
                        <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Plus ancien</option>
                    </select>

                    <?php if (!is_mobile()) : ?>
                        <button type="button" class="btn-sm2 btn-outline toggle-table-view" onclick="toggleView()">
                            <i class="fas fa-table"></i> Vue <?= $view === 'table' ? 'cartes' : 'tableau' ?>
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if (!empty($projects)) : ?>

            <?php if ($view === 'table') : ?>
                <table class="project-table">
                    <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projects as $project) : ?>
                        <tr>
                            <td><?= htmlspecialchars($project['title']) ?></td>
                            <td><?= htmlspecialchars(substr($project['description'], 0, 50)) ?>...</td>
                            <td><?= format_date($project['created_at']) ?></td>
                            <td>
                                <a href="edit-project.php?id=<?= $project['id'] ?>" class="btn-sm btn-outline">Modifier</a>
                                <a href="delete-project.php?id=<?= $project['id'] ?>" class="btn-sm btn-outline">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="manage-grid">
                    <?php foreach ($projects as $project) : ?>
                        <div class="manage-card">
                            <?php if (!empty($project['image'])) : ?>
                                <div class="manage-image">
                                    <img src="uploads/<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                                </div>
                            <?php endif; ?>
                            <div class="manage-content">
                                <h3><?= htmlspecialchars($project['title']) ?></h3>
                                <p><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>
                                <div class="manage-actions">
                                    <a href="edit-project.php?id=<?= $project['id'] ?>" class="btn-sm btn-outline">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="delete-project.php?id=<?= $project['id'] ?>" class="btn-sm btn-outline">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?= $i ?>&sort=<?= $sort ?>&view=<?= $view ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>

        <?php else : ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>Aucun projet</h3>
                <p>Vous n'avez pas encore ajouté de projet</p>
                <a href="add-project.php" class="btn btn-sm2 btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter un projet
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>