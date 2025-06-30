<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Accueil';

// Récupération des derniers portfolios
try {
    $stmt = $pdo->query("
        SELECT u.id, u.first_name, u.last_name, u.bio, u.profile_image,
               COUNT(p.id) as projects_count
        FROM users u 
        LEFT JOIN projects p ON u.id = p.user_id 
        WHERE u.role = 'user'
        GROUP BY u.id 
        ORDER BY u.created_at DESC 
        LIMIT 6
    ");
    $recent_portfolios = $stmt->fetchAll();
} catch (PDOException $e) {
    $recent_portfolios = [];
}

include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                Découvrez les <span class="highlight">portfolios</span> 
                de nos talents
            </h1>
            <p class="hero-description">
                Explorez les projets créatifs et innovants de notre communauté. 
                Partagez vos réalisations et inspirez-vous des autres.
            </p>
            <div class="hero-actions">
                <?php if (is_logged_in()): ?>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i>
                        Mon Tableau de bord
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Créer mon portfolio
                    </a>
                    <a href="login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-graphic">
                <i class="fas fa-laptop-code"></i>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title">Pourquoi choisir notre plateforme ?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h3>Design Moderne</h3>
                <p>Interface élégante et responsive pour mettre en valeur vos projets</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Sécurisé</h3>
                <p>Protection avancée contre les attaques XSS, CSRF et injections SQL</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Gestion des compétences</h3>
                <p>Système avancé de gestion des compétences avec niveaux</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Responsive</h3>
                <p>Parfaitement adapté à tous les appareils et tailles d'écran</p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($recent_portfolios)): ?>
<section class="recent-portfolios">
    <div class="container">
        <h2 class="section-title">Portfolios récents</h2>
        <div class="portfolios-grid">
            <?php foreach ($recent_portfolios as $portfolio): ?>
            <div class="portfolio-card">
                <div class="portfolio-avatar">
                    <?php if ($portfolio['profile_image']): ?>
                        <img src="uploads/<?php echo clean_input($portfolio['profile_image']); ?>" 
                             alt="<?php echo clean_input($portfolio['first_name'] . ' ' . $portfolio['last_name']); ?>">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="portfolio-info">
                    <h3><?php echo clean_input($portfolio['first_name'] . ' ' . $portfolio['last_name']); ?></h3>
                    <p class="portfolio-bio"><?php echo clean_input($portfolio['bio']); ?></p>
                    <div class="portfolio-stats">
                        <span><i class="fas fa-project-diagram"></i> <?php echo $portfolio['projects_count']; ?> projets</span>
                    </div>
                    <a href="portfolio.php?id=<?php echo $portfolio['id']; ?>" class="btn btn-outline">
                        Voir le portfolio
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="portfolios.php" class="btn btn-primary">
                Voir tous les portfolios
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>