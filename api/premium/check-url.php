<?php
// ================================================
// PREMIUM API: CHECK URL availability
// ================================================
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    echo json_encode(['available' => false, 'error' => 'URL is required']);
    exit;
}

// Sanitize
$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug));

if (strlen($slug) < 3) {
    echo json_encode(['available' => false, 'error' => 'URL must be at least 3 characters']);
    exit;
}

// Check availability (exclude the current user's own URL)
$userId = $_SESSION['premium_user_id'] ?? 0;

$stmt = mysqli_prepare($database, "SELECT id FROM premium_websites WHERE custom_url = ? AND user_id != ?");
mysqli_stmt_bind_param($stmt, 'si', $slug, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$exists = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

echo json_encode(['available' => !$exists, 'slug' => $slug]);

mysqli_close($database);
?>
