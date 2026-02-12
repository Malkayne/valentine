<?php
// ================================================
// PREMIUM API: SAVE website data
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
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

// Extract fields
$partner1Name    = trim($input['partner1Name'] ?? '');
$partner2Name    = trim($input['partner2Name'] ?? '');
$anniversaryDate = $input['anniversaryDate'] ?? null;
$websiteTitle    = trim($input['websiteTitle'] ?? '');
$welcomeMessage  = substr(trim($input['welcomeMessage'] ?? ''), 0, 250);
$template        = trim($input['template'] ?? 'romantic');
$primaryColor    = $input['primaryColor'] ?? '#FF1744';
$secondaryColor  = $input['secondaryColor'] ?? '#FFB6C1';
$accentColor     = $input['accentColor'] ?? '#FFD700';
$bgColor         = $input['bgColor'] ?? '#FFFFFF';
$fontPairing     = $input['fontPairing'] ?? 'romantic-elegant';
$storyEnabled    = $input['storyEnabled'] ? 1 : 0;
$storyContent    = $input['storyContent'] ?? '';
$galleryEnabled  = $input['galleryEnabled'] ? 1 : 0;
$galleryData     = json_encode($input['galleryData'] ?? []);
$timelineEnabled = $input['timelineEnabled'] ? 1 : 0;
$timelineData    = json_encode($input['timelineData'] ?? []);
$musicType       = $input['musicType'] ?? 'none';
$musicFile       = $input['musicFile'] ?? null;
$musicSpotifyUrl = $input['musicSpotifyUrl'] ?? null;
$customUrl       = trim($input['customUrl'] ?? '');
$passwordProtected = ($input['passwordProtected'] ?? false) ? 1 : 0;
$sitePassword    = $input['sitePassword'] ?? null;

// Sanitize custom URL
$customUrl = preg_replace('/[^a-z0-9-]/', '', strtolower($customUrl));
$customUrlVal = $customUrl !== '' ? $customUrl : null;

// Handle anniversary date
$anniversaryDateVal = null;
if ($anniversaryDate && $anniversaryDate !== '') {
    $anniversaryDateVal = $anniversaryDate;
}

// Handle password — only hash if changed (not empty and not already a hash)
$passwordVal = null;
if ($passwordProtected && $sitePassword && $sitePassword !== '' && strlen($sitePassword) < 60) {
    $passwordVal = password_hash($sitePassword, PASSWORD_DEFAULT);
} elseif ($passwordProtected && $sitePassword && strlen($sitePassword) >= 60) {
    // Already hashed, keep it
    $passwordVal = $sitePassword;
}

// Validate music type
if (!in_array($musicType, ['none', 'upload', 'spotify'])) {
    $musicType = 'none';
}

// Check if website record exists
$checkStmt = mysqli_prepare($database, "SELECT id, site_password FROM premium_websites WHERE user_id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $userId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$existing = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// If password not changed, keep existing
if ($passwordProtected && !$passwordVal && $existing && $existing['site_password']) {
    $passwordVal = $existing['site_password'];
}

if ($existing) {
    // UPDATE
    $sql = "UPDATE premium_websites SET
        partner1_name = ?, partner2_name = ?, anniversary_date = ?,
        website_title = ?, welcome_message = ?,
        template = ?, primary_color = ?, secondary_color = ?,
        accent_color = ?, bg_color = ?, font_pairing = ?,
        story_enabled = ?, story_content = ?,
        gallery_enabled = ?, gallery_data = ?,
        timeline_enabled = ?, timeline_data = ?,
        music_type = ?, music_file = ?, music_spotify_url = ?,
        custom_url = ?, password_protected = ?, site_password = ?
        WHERE user_id = ?";

    $stmt = mysqli_prepare($database, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssssssssisisisssssisi',
        $partner1Name, $partner2Name, $anniversaryDateVal,
        $websiteTitle, $welcomeMessage,
        $template, $primaryColor, $secondaryColor,
        $accentColor, $bgColor, $fontPairing,
        $storyEnabled, $storyContent,
        $galleryEnabled, $galleryData,
        $timelineEnabled, $timelineData,
        $musicType, $musicFile, $musicSpotifyUrl,
        $customUrlVal, $passwordProtected, $passwordVal,
        $userId
    );
} else {
    // INSERT
    $sql = "INSERT INTO premium_websites
        (user_id, partner1_name, partner2_name, anniversary_date,
         website_title, welcome_message,
         template, primary_color, secondary_color,
         accent_color, bg_color, font_pairing,
         story_enabled, story_content,
         gallery_enabled, gallery_data,
         timeline_enabled, timeline_data,
         music_type, music_file, music_spotify_url,
         custom_url, password_protected, site_password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($database, $sql);
    mysqli_stmt_bind_param($stmt, 'isssssssssssisisisssssis',
        $userId,
        $partner1Name, $partner2Name, $anniversaryDateVal,
        $websiteTitle, $welcomeMessage,
        $template, $primaryColor, $secondaryColor,
        $accentColor, $bgColor, $fontPairing,
        $storyEnabled, $storyContent,
        $galleryEnabled, $galleryData,
        $timelineEnabled, $timelineData,
        $musicType, $musicFile, $musicSpotifyUrl,
        $customUrlVal, $passwordProtected, $passwordVal
    );
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Saved successfully']);
} else {
    // Check for duplicate custom URL
    if (mysqli_errno($database) === 1062) {
        echo json_encode(['success' => false, 'error' => 'This custom URL is already taken']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to save: ' . mysqli_error($database)]);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($database);
?>
