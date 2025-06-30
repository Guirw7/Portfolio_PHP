<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Mes Compétences';
$user_id = $_SESSION['user_id'];

// Enregistrement des compétences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skills = $_POST['skills'] ?? [];

    $pdo->prepare("DELETE FROM user_skills WHERE user_id = ?")->execute([$user_id]);

    foreach ($skills as $skill_id => $level) {
        if (!empty($level)) {
            $stmt = $pdo->prepare("INSERT INTO user_skills (user_id, skill_id, level) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $skill_id, $level]);
        }
    }

    $success = "Compétences mises à jour.";
}

// Récupérer compétences disponibles
$stmt = $pdo->query("SELECT id, name FROM skills ORDER BY name ASC");
$all_skills = $stmt->fetchAll();

// Récupérer compétences de l'utilisateur
$stmt = $pdo->prepare("SELECT skill_id, level FROM user_skills WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_skills = [];
foreach ($stmt->fetchAll() as $row) {
    $user_skills[$row['skill_id']] = $row['level'];
}

include 'includes/header.php';
?>

<div class="container mt-5 manage-skills-wrapper">
    <form method="POST" class="skills-form">
        <h2><i class="fas fa-tools"></i> Mes Compétences</h2>

        <?php if (!empty($success)) : ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="skills-grid">
            <?php foreach ($all_skills as $skill):
                $currentLevel = $user_skills[$skill['id']] ?? 'aucun';
                ?>
                <div class="skill-card">
                    <div class="skill-header">
                        <span class="skill-name"><?= htmlspecialchars($skill['name']) ?></span>
                        <span class="badge <?= strtolower($currentLevel) ?>">
                            <?= ucfirst($currentLevel) ?>
                        </span>
                    </div>
                    <div class="skill-select">
                        <select name="skills[<?= $skill['id'] ?>]">
                            <option value="">Aucun</option>
                            <?php foreach (['debutant', 'intermediaire', 'avance', 'expert'] as $level): ?>
                                <option value="<?= $level ?>" <?= $currentLevel === $level ? 'selected' : '' ?>>
                                    <?= ucfirst($level) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
    </form>
</div>


<?php include 'includes/footer.php'; ?>
