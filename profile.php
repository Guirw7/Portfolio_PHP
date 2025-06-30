<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Mon Profil';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT first_name, last_name, bio, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $bio = trim($_POST['bio']);
    $image_name = $user['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($_FILES['profile_image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            $hash = sha1_file($_FILES['profile_image']['tmp_name']);
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $image_name = $hash . '.' . $ext;
            $destination = 'uploads/' . $image_name;

            if (!file_exists($destination)) {
                move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination);
            }
        } else {
            $error = "Image invalide.";
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, bio = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $bio, $image_name, $user_id]);
        $success = "Profil mis à jour.";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 profile-wrapper">
    <form method="post" enctype="multipart/form-data" class="profile-form">
        <h2><i class="fas fa-user-cog"></i> Mon Profil</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)) : ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Biographie</label>
            <textarea name="bio" class="form-control no-resize" rows="4"><?= htmlspecialchars($user['bio']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Image de profil</label>
            <?php if ($user['profile_image']) : ?>
                <div class="profile-image-wrapper">
                    <img src="uploads/<?= $user['profile_image'] ?>" alt="Profil" class="profile-preview">
                    <a href="delete-profile-image.php" class="btn-sm2 btn-outline mt-2">
                        <i class="fas fa-trash-alt"></i> Supprimer la photo
                    </a>
                </div>
            <?php else : ?>
                <div class="profile-preview placeholder"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <input type="file" name="profile_image" class="form-control mt-2">
        </div>

        <div class="text-end">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
        </div>
    </form>
</div>

<div class="image-modal-overlay" id="imageModal">
    <div class="image-modal-content">
        <span class="close-modal" onclick="closeImageModal()"><i class="fas fa-times"></i></span>
        <img id="zoomedImage" src="" alt="Zoom profil">
    </div>
</div>

<?php include 'includes/footer.php'; ?>
