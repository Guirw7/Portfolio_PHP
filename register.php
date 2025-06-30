<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Inscription';
$errors = [];
$success = '';

// Redirection si déjà connecté
if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = clean_input($_POST['first_name'] ?? '');
    $last_name = clean_input($_POST['last_name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Vérification CSRF
    if (!verify_csrf_token($csrf_token)) {
        $errors[] = 'Token de sécurité invalide.';
    }
    
    // Validation des champs
    if (empty($first_name)) {
        $errors[] = 'Le prénom est requis.';
    }
    
    if (empty($last_name)) {
        $errors[] = 'Le nom est requis.';
    }
    
    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format d\'email invalide.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    
    // Vérification de l'unicité de l'email
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Cet email est déjà utilisé.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de la vérification de l\'email.';
        }
    }
    
    // Inscription
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $hashed_password]);
            
            $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            
            // Reset des champs en cas de succès
            $first_name = $last_name = $email = '';
            
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de l\'inscription.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-user-plus"></i> Inscription</h1>
            <p>Créez votre compte pour commencer</p>
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
                <br><a href="login.php">Se connecter maintenant</a>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-user"></i>
                        Prénom
                    </label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?php echo clean_input($_POST['first_name'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-user"></i>
                        Nom
                    </label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?php echo clean_input($_POST['last_name'] ?? ''); ?>" 
                           required>
                </div>
            </div>
            
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
                <small>Au moins 6 caractères</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-lock"></i>
                    Confirmer le mot de passe
                </label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-user-plus"></i>
                S'inscrire
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>