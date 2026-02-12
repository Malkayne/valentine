<?php
// ================================================
// AUTH: CHECK — returns current auth state
// ================================================
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['premium_user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'id'    => $_SESSION['premium_user_id'],
            'name'  => $_SESSION['premium_user_name'],
            'email' => $_SESSION['premium_user_email']
        ]
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
