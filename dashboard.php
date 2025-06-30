<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vérification de la connexion
if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Tableau de bord';
$user_id = (int) $_SESSION['user_id'];

// Récupération des données utilisateur
try {
    // Nombre de projets
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $projects_count = $stmt->fetchColumn();

    // Nombre de compétences
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_skills WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $skills_count = $stmt->fetchColumn();

    // Derniers projets
    $stmt = $pdo->prepare("
        SELECT id, title, description, image, created_at 
        FROM projects 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $stmt->execute([$user_id]);
    $recent_projects = $stmt->fetchAll();

    // Compétences listées
    $stmt = $pdo->prepare("
        SELECT s.name, us.level 
        FROM user_skills us 
        JOIN skills s ON us.skill_id = s.id 
        WHERE us.user_id = ?
        ORDER BY us.level DESC
    ");
    $stmt->execute([$user_id]);
    $skills = $stmt->fetchAll();

} catch (PDOException $e) {
    $projects_count = $skills_count = 0;
    $recent_projects = [];
    $skills = [];
}

include 'includes/header.php';
?>

    <div class="dashboard">
        <div class="dashboard-header">
            <div class="container">
                <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
                <p>Bonjour <?php echo clean_input($_SESSION['user_name']); ?>, gérez votre portfolio ici</p>
            </div>
        </div>

        <div class="container">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $projects_count; ?></h3>
                        <p>Projets</p>
                    </div>
                    <a href="manage-projects.php" class="stat-link">Gérer</a>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-cogs"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $skills_count; ?></h3>
                        <p>Compétences</p>
                    </div>
                    <a href="manage-skills.php" class="stat-link">Gérer</a>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-eye"></i></div>
                    <div class="stat-content">
                        <h3>Public</h3>
                        <p>Portfolio</p>
                    </div>
                    <a href="portfolio.php?id=<?php echo $user_id; ?>" class="stat-link" target="_blank">Voir</a>
                </div>
            </div>

            <div class="dashboard-content">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-project-diagram"></i> Derniers projets</h2>
                        <a href="manage-projects.php" class="btn btn-outline">Voir tous</a>
                    </div>

                    <?php if (!empty($recent_projects)): ?>
                        <div class="projects-grid">
                            <?php foreach ($recent_projects as $project): ?>
                                <div class="project-card">
                                    <?php if (!empty($project['image'])): ?>
                                        <div class="project-image">
                                            <img src="uploads/<?php echo clean_input($project['image']); ?>"
                                                 alt="<?php echo clean_input($project['title']); ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="project-content">
                                        <h3><?php echo clean_input($project['title']); ?></h3>
                                        <p><?php echo clean_input(substr($project['description'], 0, 100)); ?>...</p>
                                        <div class="project-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo format_date($project['created_at']); ?>
                                        </div>
                                    </div>
                                    <div class="project-actions">
                                        <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3>Aucun projet</h3>
                            <p>Commencez par créer votre premier projet</p>
                            <a href="add-project.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter un projet
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-chart-bar"></i> Répartition des compétences</h2>
                        <a href="manage-skills.php" class="btn btn-outline">Gérer</a>
                    </div>

                    <?php if (!empty($skills)): ?>
                        <div class="skills-list">
                            <ul class="list-group">
                                <?php foreach ($skills as $skill): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo htmlspecialchars($skill['name']); ?> (<?php echo htmlspecialchars($skill['level']); ?>)</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-cogs"></i>
                            <h3>Aucune compétence</h3>
                            <p>Ajoutez vos compétences pour enrichir votre profil</p>
                            <a href="manage-skills.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter des compétences
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-actions">
                    <a href="profile.php" class="action-card">
                        <div class="action-icon"><i class="fas fa-user-edit"></i></div>
                        <div class="action-content">
                            <h3>Modifier le profil</h3>
                            <p>Mettez à jour vos informations personnelles</p>
                        </div>
                    </a>
                    <a href="add-project.php" class="action-card">
                        <div class="action-icon"><i class="fas fa-plus-circle"></i></div>
                        <div class="action-content">
                            <h3>Nouveau projet</h3>
                            <p>Ajoutez un nouveau projet à votre portfolio</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>