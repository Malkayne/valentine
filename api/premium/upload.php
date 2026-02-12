<?php
// ================================================
// PREMIUM API: UPLOAD gallery images/video/music
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
$uploadType = $_POST['type'] ?? 'image'; // image, video, music

// Create user upload directory
$userDir = UPLOADS_DIR . '/premium/' . $userId;
if (!is_dir($userDir)) {
    mkdir($userDir, 0755, true);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validate by type
if ($uploadType === 'image') {
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    if (!in_array($ext, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid image type. Use JPG, PNG, GIF, or WebP']);
        exit;
    }
} elseif ($uploadType === 'video') {
    $allowedTypes = ['mp4', 'webm', 'mov'];
    $maxSize = 20 * 1024 * 1024; // 20MB
    if (!in_array($ext, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid video type. Use MP4, WebM, or MOV']);
        exit;
    }
} elseif ($uploadType === 'music') {
    $allowedTypes = ['mp3', 'ogg', 'wav', 'm4a'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    if (!in_array($ext, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid audio type. Use MP3, OGG, WAV, or M4A']);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid upload type']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    $maxMB = $maxSize / 1024 / 1024;
    echo json_encode(['success' => false, 'error' => "File must be under {$maxMB}MB"]);
    exit;
}

// Generate unique filename
$filename = $uploadType . '_' . uniqid() . '.' . $ext;
$destination = $userDir . '/' . $filename;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $relativePath = 'uploads/premium/' . $userId . '/' . $filename;

    echo json_encode([
        'success'  => true,
        'filename' => $filename,
        'path'     => $relativePath,
        'url'      => BASE_URL . '/' . $relativePath
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
}
?>
