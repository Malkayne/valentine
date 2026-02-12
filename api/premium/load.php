<?php
// ================================================
// PREMIUM API: LOAD website data
// ================================================
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['premium_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['premium_user_id'];

$stmt = mysqli_prepare($database, "SELECT * FROM premium_websites WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$website = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$website) {
    echo json_encode(['success' => true, 'data' => null]);
    exit;
}

// Decode JSON fields
$website['gallery_data'] = json_decode($website['gallery_data'], true) ?: [];
$website['timeline_data'] = json_decode($website['timeline_data'], true) ?: [];

// Don't send password hash to client
$website['site_password'] = $website['site_password'] ? '••••••' : '';

echo json_encode(['success' => true, 'data' => $website]);

mysqli_close($database);
?>
