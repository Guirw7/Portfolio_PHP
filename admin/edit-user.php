<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Modifier un utilisateur';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('users.php');
}

$user_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT first_name, last_name, email, role, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    redirect('users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $delete_image = isset($_POST['delete_image']);
    $image_name = $user['profile_image'];

    if (empty($first_name) || empty($last_name) || empty($email) || !in_array($role, ['admin', 'user'])) {
        $error = "Tous les champs sont requis.";
    } else {
        // Supprimer image si coché
        if ($delete_image && $image_name) {
            $old_path = '../uploads/' . $image_name;
            if (file_exists($old_path)) {
                unlink($old_path);
            }
            $image_name = null;
        }

        // Nouvelle image
        if (!empty($_FILES['profile_image']['name'])) {
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0755, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['profile_image']['type'], $allowed_types)) {
                $hash = sha1_file($_FILES['profile_image']['tmp_name']);
                $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $new_image_name = $hash . '.' . $ext;
                $destination = '../uploads/' . $new_image_name;

                if (!file_exists($destination)) {
                    move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination);
                }

                // Supprimer l'ancienne image si différente
                if ($image_name && $image_name !== $new_image_name) {
                    $old_path = '../uploads/' . $image_name;
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }

                $image_name = $new_image_name;
            } else {
                $error = "Format d’image non valide.";
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, profile_image = ? WHERE id = ?");
                $stmt->execute([$first_name, $last_name, $email, $role, $image_name, $user_id]);
                $success = "Utilisateur mis à jour avec succès.";
                // Recharger
                $stmt = $pdo->prepare("SELECT first_name, last_name, email, role, profile_image FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour.";
            }
        }
    }
}



include '../includes/header.php';
?>

    <div class="container-centered">
        <div class="form-card users">
            <h1>Modifier l'utilisateur #<?= $user_id ?></h1>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php elseif (!empty($success)) : ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role" class="form-control">
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image de profil</label>
                    <div class="image-input-group">
                        <?php if (!empty($user['profile_image'])): ?>
                            <div class="profile-preview-admin">
                                <img src="../uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Image de profil">
                                <label class="checkbox">
                                    <input type="checkbox" name="delete_image"> Supprimer l’image actuelle
                                </label>
                            </div>
                        <?php endif; ?>

                        <input type="file" name="profile_image" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="users.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>