<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) redirect('../login.php');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) redirect('projects.php');

$project_id = (int)$_GET['id'];
$page_title = "Suppression du projet #$project_id";

// Récupération projet
$stmt = $pdo->prepare("SELECT p.title, p.image, u.password FROM projects p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) redirect('projects.php');

// Phrase de confirmation (non copiable)
$required_phrase = "Je confirme la suppression définitive du projet #$project_id";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_phrase = trim($_POST['confirmation'] ?? '');
    $admin_pass = trim($_POST['admin_password'] ?? '');

    if ($input_phrase !== $required_phrase) {
        $error = "La phrase de confirmation est incorrecte.";
    } elseif (!password_verify($admin_pass, $_SESSION['user_password'])) { // Vérifie mot de passe stocké en session
        $error = "Mot de passe incorrect.";
    } else {
        // Supprime l'image
        if (!empty($project['image'])) {
            $path = '../uploads/' . $project['image'];
            if (file_exists($path)) unlink($path);
        }

        // Supprime le projet
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);

        $success = "Projet supprimé avec succès. Redirection en cours...";
        echo "<script>
            setTimeout(() => {
                window.location.href = 'projects.php';
            }, 2000);
        </script>";
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="form-card">
        <h2 class="mb-3 text-danger"><i class="fas fa-exclamation-triangle"></i> Suppression du projet #<?= $project_id ?></h2>

        <p>⚠️ Vous êtes sur le point de supprimer le projet <strong><?= htmlspecialchars($project['title']) ?></strong>.</p>

        <p>Pour confirmer cette suppression, veuillez :</p>
        <ol>
            <li>✍️ Taper <strong>à la main</strong> la phrase ci-dessous</li>
            <li>🔐 Entrer votre mot de passe admin</li>
        </ol>

        <div class="alert alert-warning">
            <code style="user-select: none; pointer-events: none;"><?= $required_phrase ?></code>
        </div>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)) : ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
                <span class="spinner-border spinner-border-sm text-success" role="status"></span>
                <?= $success ?>
            </div>
        <?php else : ?>
            <form method="post" class="mt-4">
                <div class="form-group mb-3">
                    <label for="confirmation">Phrase de confirmation</label>
                    <input type="text" name="confirmation" id="confirmation" class="form-control" required autocomplete="off" autofocus>
                </div>

                <div class="form-group mb-3">
                    <label for="admin_password">Mot de passe admin</label>
                    <input type="password" name="admin_password" id="admin_password" class="form-control" required autocomplete="off">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash-alt"></i> Supprimer définitivement
                    </button>
                    <a href="projects.php" class="btn btn-secondary w-100">Annuler</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
