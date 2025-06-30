<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Supprimer un projet';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('manage-projects.php');
}

$project_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Vérification que le projet appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT title FROM projects WHERE id = ? AND user_id = ?");
$stmt->execute([$project_id, $user_id]);
$project = $stmt->fetch();

if (!$project) {
    redirect('manage-projects.php');
}

// Suppression si confirmé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete = $pdo->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
    $delete->execute([$project_id, $user_id]);
    redirect('manage-projects.php');
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="card confirm-delete">
        <h2><i class="fas fa-trash-alt"></i> Supprimer un projet</h2>
        <p>Êtes-vous sûr de vouloir supprimer le projet <strong><?= htmlspecialchars($project['title']) ?></strong> ?</p>
        <form method="post" class="confirm-actions">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Oui, supprimer
            </button>
            <a href="manage-projects.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Annuler
            </a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
