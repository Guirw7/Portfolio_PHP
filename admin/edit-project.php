<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) redirect('../login.php');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) redirect('projects.php');

$id = (int)$_GET['id'];
$page_title = "Modifier le projet #$id";

$stmt = $pdo->prepare("SELECT p.*, u.first_name, u.last_name, u.profile_image, u.id AS user_id
                       FROM projects p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) redirect('projects.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    $delete_image = isset($_POST['delete_image']);
    $image = $project['image'];

    if ($delete_image && $image) {
        $path = '../uploads/' . $image;
        if (file_exists($path)) unlink($path);
        $image = null;
    }

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new = sha1_file($_FILES['image']['tmp_name']) . '.' . $ext;
            $dest = '../uploads/' . $new;
            if (!file_exists($dest)) move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            if ($image && $image !== $new) {
                $old = '../uploads/' . $image;
                if (file_exists($old)) unlink($old);
            }
            $image = $new;
        } else {
            $error = "Format d’image invalide.";
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, link=?, image=? WHERE id=?");
        $stmt->execute([$title, $description, $link, $image, $id]);
        $success = "Projet mis à jour.";

        // Recharge les données à jour
        $stmt = $pdo->prepare("SELECT p.*, u.first_name, u.last_name, u.profile_image, u.id AS user_id
                           FROM projects p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
    }

}

include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="edit-project-wrapper">
        <div class="edit-project-owner">
            <div class="owner-avatar">
                <?php if ($project['profile_image']) : ?>
                    <img src="../uploads/<?= $project['profile_image'] ?>" alt="Avatar">
                <?php else : ?>
                    <img src="../uploads/default.png" alt="Avatar">
                <?php endif; ?>
            </div>
            <div class="owner-info">
                <strong><?= htmlspecialchars($project['first_name'] . ' ' . $project['last_name']) ?></strong>
                <a href="../portfolio.php?id=<?= $project['user_id'] ?>" target="_blank" class="owner-link">Voir le portfolio</a>
                <p class="owner-date"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($project['created_at'])) ?></p>
            </div>
            <a href="delete-project.php?id=<?= $id ?>" class="btn btn-danger w-100 mt-3" onclick="return confirm('Supprimer ce projet ?')">
                <i class="fas fa-trash-alt"></i> Supprimer
            </a>
        </div>

        <div class="edit-project-form">
            <h2><i class="fas fa-edit"></i> Modifier le projet #<?= $id ?></h2>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif (!empty($success)) : ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($project['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($project['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Lien du projet</label>
                    <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($project['link']) ?>">
                </div>

                <div class="form-group image-block">
                    <label>Image actuelle</label>
                    <div class="image-delete-wrapper">
                        <?php if ($project['image']) : ?>
                            <img src="../uploads/<?= $project['image'] ?>" alt="Image actuelle" class="project-thumb" onclick="openImageModal(this.src)">
                            <div class="delete-image-control">
                                <input type="checkbox" name="delete_image" id="delete_image">
                                <label for="delete_image">Supprimer l'image</label>
                            </div>
                        <?php else : ?>
                            <p><em>Aucune image</em></p>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="image" class="form-control mt-2">
                </div>


                <div class="form-buttons mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="projects.php" class="btn btn-secondary w-100">Retour</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal image -->
<div class="image-modal-overlay" id="imageModal" onclick="closeImageModal()">
    <img id="imageModalContent" src="" alt="Zoom image">
</div>

<script src="../assets/js/script.js"></script>

<?php include '../includes/footer.php'; ?>
