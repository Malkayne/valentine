<?php
// ================================================
// API: RESPOND TO VALENTINE (YES clicked)
// POST — marks as responded, sends email to sender
// ================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/mailer.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Read JSON body or POST data
$input = json_decode(file_get_contents('php://input'), true);
$uniqueId = trim($input['uniqueId'] ?? ($_POST['uniqueId'] ?? ''));

if ($uniqueId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing valentine ID']);
    exit;
}

// Fetch the valentine record
$stmt = mysqli_prepare($database, "SELECT * FROM valentines WHERE unique_id = ?");
mysqli_stmt_bind_param($stmt, 's', $uniqueId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$valentine = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$valentine) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Valentine not found']);
    exit;
}

// Already responded?
if ($valentine['responded']) {
    echo json_encode(['success' => true, 'message' => 'Already responded!']);
    exit;
}

// Mark as responded
$updateStmt = mysqli_prepare($database,
    "UPDATE valentines SET responded = 1, responded_at = NOW() WHERE unique_id = ?"
);
mysqli_stmt_bind_param($updateStmt, 's', $uniqueId);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

// Send email notification to sender (if email was provided)
$emailSent = false;
if (!empty($valentine['sender_email'])) {
    $recipientName = htmlspecialchars($valentine['recipient_name']);
    $senderName    = htmlspecialchars($valentine['sender_name']);

    $subject = "💕 {$recipientName} said YES to your Valentine!";

    $htmlBody = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 30px;'>
        <div style='text-align: center; padding: 30px; background: linear-gradient(135deg, #FF69B4, #FF1744); border-radius: 15px; margin-bottom: 25px;'>
            <h1 style='color: white; margin: 0; font-size: 2rem;'>🎉 They Said YES! 🎉</h1>
        </div>

        <div style='background: #FFF0F5; padding: 25px; border-radius: 15px; text-align: center;'>
            <p style='font-size: 1.3rem; color: #333; margin-bottom: 10px;'>
                Hey <strong>{$senderName}</strong>,
            </p>
            <p style='font-size: 1.1rem; color: #555;'>
                Great news! <strong>{$recipientName}</strong> accepted your Valentine's question:
            </p>
            <p style='font-size: 1.4rem; color: #FF1744; font-style: italic; margin: 20px 0;'>
                \"{$valentine['question']}\"
            </p>
            <p style='font-size: 3rem; margin: 15px 0;'>💕❤️💕</p>
            <p style='font-size: 1rem; color: #888;'>
                Happy Valentine's Day! 🌹
            </p>
        </div>

        <div style='text-align: center; margin-top: 25px; color: #aaa; font-size: 0.85rem;'>
            <p>Sent by Valentine's Special &bull; Made with ❤️</p>
        </div>
    </div>";

    $emailSent = sendMail(
        $valentine['sender_email'],
        $valentine['sender_name'],
        $subject,
        $htmlBody
    );
}

echo json_encode([
    'success'    => true,
    'message'    => 'Response recorded!',
    'emailSent'  => $emailSent
]);

mysqli_close($database);
?>
