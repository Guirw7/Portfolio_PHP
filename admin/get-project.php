<?php
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("
    SELECT p.*, u.first_name, u.last_name
    FROM projects p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    echo json_encode(['error' => 'Projet introuvable']);
    exit;
}

echo json_encode([
    'title' => $project['title'],
    'description' => $project['description'],
    'author' => $project['first_name'] . ' ' . $project['last_name'],
    'date' => date('d/m/Y', strtotime($project['created_at'])),
    'image' => $project['image'] ?? null
]);
