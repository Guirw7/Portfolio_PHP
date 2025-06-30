<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Vérification admin
if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

$page_title = 'Administration';

// Statistiques générales
try {
    // Nombre total d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $total_users = $stmt->fetchColumn();
    
    // Nombre total de projets
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
    $total_projects = $stmt->fetchColumn();
    
    // Nombre total de compétences
    $stmt = $pdo->query("SELECT COUNT(*) FROM skills");
    $total_skills = $stmt->fetchColumn();
    
    // Derniers utilisateurs
    $stmt = $pdo->query("
        SELECT id, first_name, last_name, email, created_at 
        FROM users 
        WHERE role = 'user' 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_users = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_users = $total_projects = $total_skills = 0;
    $recent_users = [];
}

include '../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <div class="container">
            <h1>
                <i class="fas fa-cog"></i>
                Administration
            </h1>
            <p>Panneau de contrôle administrateur</p>
        </div>
    </div>
    
    <div class="container">
        <div class="admin-nav">
            <a href="../index.php" class="admin-nav-link active">
                <i class="fas fa-tachometer-alt"></i>
                Tableau de bord
            </a>
            <a href="../admin/users.php" class="admin-nav-link">
                <i class="fas fa-users"></i>
                Utilisateurs
            </a>
            <a href="../admin/skills.php" class="admin-nav-link">
                <i class="fas fa-cogs"></i>
                Compétences
            </a>
            <a href="../admin/projects.php" class="admin-nav-link">
                <i class="fas fa-project-diagram"></i>
                Projets
            </a>
        </div>
        
        <div class="admin-stats">
            <div class="admin-stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Utilisateurs</p>
                </div>
                <a href="../admin/users.php" class="stat-link">Gérer</a>
            </div>
            
            <div class="admin-stat-card">
                <div class="stat-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $total_projects; ?></h3>
                    <p>Projets</p>
                </div>
                <a href="../admin/projects.php" class="stat-link">Gérer</a>
            </div>
            
            <div class="admin-stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $total_skills; ?></h3>
                    <p>Compétences</p>
                </div>
                <a href="../admin/skills.php" class="stat-link">Gérer</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-clock"></i> Derniers utilisateurs</h2>
                    <a href="../admin/users.php" class="btn btn-outline">Voir tous</a>
                </div>
                
                <?php if (!empty($recent_users)): ?>
                    <div class="users-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td><?php echo clean_input($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo clean_input($user['email']); ?></td>
                                    <td><?php echo format_date($user['created_at']); ?></td>
                                    <td>
                                        <a href="../portfolio.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm" target="_blank">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <a href="../admin/edit-user.php?id=<?php echo $user['id']; ?>"
                                           class="btn btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>Aucun utilisateur</h3>
                        <p>Les nouveaux utilisateurs apparaîtront ici</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="admin-actions">
                <a href="../admin/add-skill.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-content">
                        <h3>Nouvelle compétence</h3>
                        <p>Ajouter une compétence au système</p>
                    </div>
                </a>
                
                <a href="../admin/users.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="action-content">
                        <h3>Gérer les utilisateurs</h3>
                        <p>Modifier ou supprimer des comptes</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>