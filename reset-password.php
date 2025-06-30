<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = "Réinitialiser le mot de passe";
$errors = [];
$success = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');

if (!$token) {
    $errors[] = "Lien invalide ou incomplet.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token) {
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($password) || empty($password2)) {
        $errors[] = "Veuillez saisir et confirmer votre nouveau mot de passe.";
    } elseif ($password !== $password2) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Vérification du token (et expiration)
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        if ($reset) {
            // On modifie le mot de passe (hashé)
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                ->execute([$hashed, $reset['user_id']]);
            // On supprime le token pour éviter réutilisation
            $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$reset['user_id']]);
            $success = "Votre mot de passe a bien été modifié.<br><a href='login.php'>Se connecter</a>";
        } else {
            $errors[] = "Lien de réinitialisation invalide ou expiré.";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Réinitialisation du mot de passe</h1>
        <?php if ($errors): ?>
            <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($token): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" name="password" id="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="password2">Confirmer le mot de passe</label>
                    <input type="password" name="password2" id="password2" required minlength="6">
                </div>
                <button class="btn btn-primary btn-full">Réinitialiser</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
