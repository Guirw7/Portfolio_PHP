<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$page_title = "Mot de passe oublié";
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');

    if (empty($email)) {
        $errors[] = "Veuillez saisir votre adresse email.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Efface les anciens tokens de ce user
            $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user['id']]);

            $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)")
                ->execute([$user['id'], $token, $expires]);

            $reset_link = "http://localhost:8000/reset-password.php?token=$token"; // Change en prod

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
                $mail->Port = $_ENV['MAIL_PORT'];
                $mail->CharSet = 'UTF-8';
                $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body = "
        <h2>Réinitialisation du mot de passe</h2>
        <p>Bonjour,</p>
        <p>Pour réinitialiser votre mot de passe, cliquez sur ce lien :</p>
        <p><a href='$reset_link'>$reset_link</a></p>
        <p>Ce lien expirera dans 1 heure.</p>
        <hr>
        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
    ";
                $mail->send();
                $success = "Un email de réinitialisation a été envoyé.";
            } catch (Exception $e) {
                $errors[] = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
            }

        } else {
            $success = "Un email de réinitialisation a été envoyé si ce compte existe.";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Mot de passe oublié</h1>
        <?php if ($errors): ?>
            <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Votre email</label>
                    <input type="email" name="email" required>
                </div>
                <button class="btn btn-primary btn-full">Recevoir le lien</button>
            </form>
        <?php endif; ?>
        <p><a href="./login.php">Retour à la connexion</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
