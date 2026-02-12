<?php
// ================================================
// AUTH: REGISTER
// ================================================
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$name     = trim($input['name'] ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

// Validate
if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

// Check if email exists
$stmt = mysqli_prepare($database, "SELECT id FROM premium_users WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($result)) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Email already registered. Please login instead.']);
    mysqli_stmt_close($stmt);
    exit;
}
mysqli_stmt_close($stmt);

// Create user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($database, "INSERT INTO premium_users (name, email, password) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    $userId = mysqli_insert_id($database);

    // Start session
    $_SESSION['premium_user_id'] = $userId;
    $_SESSION['premium_user_name'] = $name;
    $_SESSION['premium_user_email'] = $email;

    // Create empty website record
    $stmt2 = mysqli_prepare($database, "INSERT INTO premium_websites (user_id) VALUES (?)");
    mysqli_stmt_bind_param($stmt2, 'i', $userId);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    echo json_encode([
        'success' => true,
        'user' => ['id' => $userId, 'name' => $name, 'email' => $email]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again.']);
}

mysqli_stmt_close($stmt);
mysqli_close($database);
?>
