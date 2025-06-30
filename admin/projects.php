<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Tous les Projets';

$per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

// Total count
$total_stmt = $pdo->query("SELECT COUNT(*) FROM projects");
$total = $total_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// Projects list
$stmt = $pdo->prepare("SELECT p.id, p.title, p.description, p.image, p.link, p.created_at, u.first_name, u.last_name
    FROM projects p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="container mt-5 manage-projects-wrapper">
    <div class="manage-section">
        <div class="manage-header">
            <h2><i class="fas fa-folder-open"></i> Tous les Projets</h2>
        </div>

        <?php if (empty($projects)) : ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>Aucun projet trouvé</h3>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="project-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= $project['id'] ?></td>
                            <td><?= htmlspecialchars($project['title']) ?></td>
                            <td><?= htmlspecialchars($project['first_name'] . ' ' . $project['last_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($project['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-outline" onclick='openProjectModal(<?= json_encode($project) ?>)'>
                                    Voir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL -->
<div class="project-modal-overlay" id="projectModal">
    <div class="project-modal">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <img id="modalImage" src="" alt="Preview du projet">
        <h2 id="modalTitle"></h2>
        <p id="modalDescription"></p>
        <div class="modal-actions">
            <a id="editLink" href="#" class="btn btn-outline"><i class="fas fa-edit"></i> Modifier</a>
            <a id="deleteLink" href="#" class="btn btn-danger" onclick="return confirm('Supprimer ce projet ?')"><i class="fas fa-trash"></i> Supprimer</a>
        </div>
        <a id="modalLink" href="#" target="_blank" class="modal-link">Voir le projet complet</a>
        <span id="modalNoLink" class="modal-no-link" style="display: none;">Aucun lien disponible</span>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<?php include '../includes/footer.php'; ?>
