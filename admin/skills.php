<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Gestion des Compétences';

try {
    $stmt = $pdo->query("SELECT id, name FROM skills ORDER BY name ASC");
    $skills = $stmt->fetchAll();
} catch (PDOException $e) {
    $skills = [];
    $error = "Erreur lors de la récupération des compétences.";
}

include '../includes/header.php';

$sort = $_GET['sort'] ?? 'name_asc';

$order = match ($sort) {
    'name_asc' => 'name ASC',
    'name_desc' => 'name DESC',
    'id_asc' => 'id ASC',
    'id_desc' => 'id DESC',
    default => 'name ASC',
};

$stmt = $pdo->query("SELECT id, name FROM skills ORDER BY $order");
$skills = $stmt->fetchAll();
?>

<div class="container-centered">
    <div class="form-card skills-list">
        <div class="skills-header">
            <h2><i class="fas fa-cogs"></i> Liste des Compétences</h2>
            <div class="skills-controls">
                <a href="add-skill.php" class="btn-sm2 btn-primary">
                    <i class="fas fa-plus-circle"></i> Ajouter une compétence
                </a>
                <select onchange="location = '?sort=' + this.value;" class="btn-sm2 btn-outline">
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Nom A → Z</option>
                    <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Nom Z → A</option>
                    <option value="id_asc" <?= $sort === 'id_asc' ? 'selected' : '' ?>>ID croissant</option>
                    <option value="id_desc" <?= $sort === 'id_desc' ? 'selected' : '' ?>>ID décroissant</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="styled-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($skills as $skill): ?>
                    <tr>
                        <td><?= $skill['id'] ?></td>
                        <td><?= htmlspecialchars($skill['name']) ?></td>
                        <td>
                            <a href="delete-skill.php?id=<?= $skill['id'] ?>" class="btn-sm2 btn-outline-danger"
                               onclick="return confirm('Supprimer cette compétence ?');">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>
