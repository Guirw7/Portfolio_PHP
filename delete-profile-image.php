<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user && $user['profile_image']) {
    $filepath = 'uploads/' . $user['profile_image'];
    if (file_exists($filepath)) {
        unlink($filepath);
    }

    $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?")->execute([$user_id]);
}

redirect('profile.php');
