<?php
// ================================================
// PREMIUM API: PUBLISH website
// ================================================
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['premium_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['premium_user_id'];

// Load website
$stmt = mysqli_prepare($database, "SELECT * FROM premium_websites WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$website = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$website) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'No website found. Please save first.']);
    exit;
}

// Validate required fields
$errors = [];
if (!$website['partner1_name']) $errors[] = 'Partner 1 name';
if (!$website['partner2_name']) $errors[] = 'Partner 2 name';
if (!$website['website_title']) $errors[] = 'Website title';
if (!$website['custom_url']) $errors[] = 'Custom URL';

if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Please complete these required fields: ' . implode(', ', $errors)
    ]);
    exit;
}

// Mark as published
$updateStmt = mysqli_prepare($database, "UPDATE premium_websites SET is_published = 1 WHERE user_id = ?");
mysqli_stmt_bind_param($updateStmt, 'i', $userId);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

$liveUrl = BASE_URL . '/love/' . $website['custom_url'];

echo json_encode([
    'success' => true,
    'url'     => $liveUrl,
    'message' => 'Your love website is now live!'
]);

mysqli_close($database);
?>
