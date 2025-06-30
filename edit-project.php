<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Modifier un Projet';
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('manage-projects.php');
}

$project_id = (int)$_GET['id'];

// Récupérer le projet existant
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND user_id = ?");
$stmt->execute([$project_id, $user_id]);
$project = $stmt->fetch();

if (!$project) {
    redirect('manage-projects.php');
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    $image_name = $project['image'];

    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            $hash = sha1_file($_FILES['image']['tmp_name']);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = $hash . '.' . $ext;
            $destination = 'uploads/' . $image_name;

            if (!file_exists($destination)) {
                move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            }
        } else {
            $error = "Format d’image non valide.";
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, link = ?, image = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $description, $link, $image_name, $project_id, $user_id]);
        $success = "Projet mis à jour avec succès.";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Modifier le Projet</h1>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif (!empty($success)) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="card p-4 shadow">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($project['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($project['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Lien externe</label>
            <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($project['link']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Image actuelle :</label><br>
            <?php if ($project['image']) : ?>
                <img src="uploads/<?= $project['image'] ?>" class="img-thumbnail mb-2" width="200">
            <?php else : ?>
                <em>Aucune image</em>
            <?php endif; ?>
            <input type="file" name="image" class="form-control mt-2">
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="manage-projects.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
