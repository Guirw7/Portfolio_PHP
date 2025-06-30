<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Supprimer la session de la base de données si elle existe
if (isset($_COOKIE['remember_token'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->execute([$_COOKIE['remember_token']]);
        setcookie('remember_token', '', time() - 3600, '/');
    } catch (PDOException $e) {
        // Erreur silencieuse
    }
}

// Détruire la session
session_destroy();

// Redirection
redirect('index.php');
?>