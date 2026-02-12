<?php
// ================================================
// API: CREATE VALENTINE
// POST — saves valentine to DB, returns unique link
// ================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Read fields (supports both FormData and JSON)
$senderName    = trim($_POST['senderName']    ?? '');
$recipientName = trim($_POST['recipientName'] ?? '');
$senderEmail   = trim($_POST['senderEmail']   ?? '');
$question      = trim($_POST['question']      ?? 'Will you be my Valentine?');
$theme         = trim($_POST['theme']         ?? 'theme-romantic-red');

// Validate required fields
if ($senderName === '' || $recipientName === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Sender name and recipient name are required']);
    exit;
}

if ($senderEmail !== '' && !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Generate unique ID
function generateUniqueId($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $id;
}

$uniqueId = generateUniqueId();

// Handle image upload (optional)
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];

    // Validate type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid image type. Use JPG, PNG, GIF, or WebP']);
        exit;
    }

    // Validate size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Image must be under 5MB']);
        exit;
    }

    // Save file
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $uniqueId . '.' . $ext;
    $destination = UPLOADS_DIR . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $imagePath = 'uploads/' . $filename;
    }
}

// Insert into database
$stmt = mysqli_prepare($database,
    "INSERT INTO valentines (unique_id, sender_name, recipient_name, sender_email, question, theme, image_path)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$emailVal = $senderEmail !== '' ? $senderEmail : null;
$imageVal = $imagePath;

mysqli_stmt_bind_param($stmt, 'sssssss',
    $uniqueId,
    $senderName,
    $recipientName,
    $emailVal,
    $question,
    $theme,
    $imageVal
);

if (mysqli_stmt_execute($stmt)) {
    $viewUrl = BASE_URL . '/free-tier/view.php?id=' . $uniqueId;

    echo json_encode([
        'success'   => true,
        'uniqueId'  => $uniqueId,
        'url'       => $viewUrl
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save. Please try again.']);
}

mysqli_stmt_close($stmt);
mysqli_close($database);
?>
