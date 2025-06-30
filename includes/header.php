<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Portfolio ESGI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php">
                    <i class="fas fa-portfolio"></i>
                    Portfolio ESGI
                </a>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="../portfolios.php" class="nav-link">Portfolios</a>
                
                <?php if (is_logged_in()): ?>
                    <a href="../dashboard.php" class="nav-link">Tableau de bord</a>
                    <?php if (is_admin()): ?>
                        <a href="../admin" class="nav-link">Administration</a>
                    <?php endif; ?>
                    <a href="../logout.php" class="nav-link">Déconnexion</a>
                    <span class="nav-user">
                        <i class="fas fa-user"></i>
                        <?php echo clean_input($_SESSION['user_name']); ?>
                    </span>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Connexion</a>
                    <a href="register.php" class="nav-link btn-primary">Inscription</a>
                <?php endif; ?>
            </div>
            <div class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
    
    <main class="main-content">