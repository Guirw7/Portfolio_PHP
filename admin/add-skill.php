<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Ajouter une compétence';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (empty($name)) {
        $error = "Le nom de la compétence est requis.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO skills (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "Compétence ajoutée avec succès.";
        } catch (PDOException $e) {
            $error = "Erreur lors de l’ajout de la compétence.";
        }
    }
}

include '../includes/header.php';
?>

<div class="container-centered">
    <div class="form-card">
        <h2><i class="fas fa-plus-circle"></i> Ajouter une Compétence</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php elseif (!empty($success)) : ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow">
            <div class="form-group mb-4">
                <label for="name" class="form-label">Nom de la compétence</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="Ex: JavaScript, Figma...">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Ajouter
                </button>
                <a href="skills.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
