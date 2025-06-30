<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Supprimer une compétence';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('skills.php');
}

$skill_id = (int) $_GET['id'];

// Récupérer la compétence
$stmt = $pdo->prepare("SELECT name FROM skills WHERE id = ?");
$stmt->execute([$skill_id]);
$skill = $stmt->fetch();

if (!$skill) {
    redirect('skills.php');
}

// Suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
        $stmt->execute([$skill_id]);
        redirect('skills.php');
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression.";
    }
}

include '../includes/header.php';
?>

<div class="container-centered">
    <div class="form-card">
        <h2><i class="fas fa-trash-alt"></i> Supprimer une compétence</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php else : ?>
            <p style="padding: 1rem;">Êtes-vous sûr de vouloir supprimer la compétence <strong>TEST</strong> ?</p>
            <form method="post" class="form-actions">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-check-circle"></i> Oui, supprimer
                </button>
                <a href="skills.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
