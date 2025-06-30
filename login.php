<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Connexion';
$errors = [];
$success = '';

// Redirection si déjà connecté
if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Vérification CSRF
    if (!verify_csrf_token($csrf_token)) {
        $errors[] = 'Token de sécurité invalide.';
    }
    
    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, email, password, first_name, last_name, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_password'] = $user['password']; // pour vérification dans delete

                // Cookie "se souvenir de moi"
                if ($remember) {
                    $session_id = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                    
                    $stmt = $pdo->prepare("INSERT INTO sessions (id, user_id, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$session_id, $user['id'], $expires]);
                    
                    setcookie('remember_token', $session_id, strtotime('+30 days'), '/', '', false, true);
                }
                
                redirect($user['role'] === 'admin' ? 'admin/' : 'dashboard.php');
            } else {
                $errors[] = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de la connexion.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-sign-in-alt"></i> Connexion</h1>
            <p>Accédez à votre tableau de bord</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo clean_input($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo clean_input($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i>
                    Email
                </label>
                <input type="email" id="email" name="email" 
                       value="<?php echo clean_input($_POST['email'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Mot de passe
                </label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span class="checkmark"></span>
                    Se souvenir de moi
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-sign-in-alt"></i>
                Se connecter
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
            <p><a href="forgot-password.php">Mot de passe oublié ?</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>