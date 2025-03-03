<?php
require_once '../db.php';
require_once '../auth.php';

if (!isManager()) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDbConnection();
$parkId = $_GET['id'] ?? null;
if ($parkId) {
    $stmt = $pdo->prepare("DELETE FROM parks WHERE id = ?");
    $stmt->execute([$parkId]);
}

header('Location: list.php');
exit;