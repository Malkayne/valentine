<?php
// ================================================
// AUTH: LOGIN
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
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

// Find user
$stmt = mysqli_prepare($database, "SELECT id, name, email, password FROM premium_users WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    exit;
}

// Start session
$_SESSION['premium_user_id'] = $user['id'];
$_SESSION['premium_user_name'] = $user['name'];
$_SESSION['premium_user_email'] = $user['email'];

echo json_encode([
    'success' => true,
    'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]
]);

mysqli_close($database);
?>
