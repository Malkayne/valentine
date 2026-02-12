<?php
// ================================================
// FREE-TIER VIEW PAGE — Loads valentine from DB
// ================================================
require_once __DIR__ . '/../config.php';

$uniqueId = $_GET['id'] ?? '';
$valentine = null;

if ($uniqueId !== '') {
    $stmt = mysqli_prepare($database, "SELECT * FROM valentines WHERE unique_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $uniqueId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $valentine = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Fallback defaults if not found (for demo / direct access)
$senderName    = $valentine['sender_name']    ?? 'Someone Special';
$recipientName = $valentine['recipient_name'] ?? 'You';
$question      = $valentine['question']       ?? 'Will you be my Valentine?';
$theme         = $valentine['theme']          ?? 'theme-romantic-red';
$imagePath     = $valentine['image_path']     ?? 'assets/images/default-love.gif';
$responded     = $valentine['responded']      ?? 0;

// Build image URL
$imageUrl = 'https://via.placeholder.com/200/FF69B4/FFFFFF?text=%E2%9D%A4%EF%B8%8F';
if ($imagePath) {
    $imageUrl = BASE_URL . '/' . $imagePath;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($question); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($senderName); ?> has a special question for <?php echo htmlspecialchars($recipientName); ?>!">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/free-tier.css">
</head>
<body>
    <div class="heart-bg"></div>
    <div class="question-page <?php echo htmlspecialchars($theme); ?>">
        <!-- Question Content -->
        <div class="question-content" id="questionContent" <?php if ($responded): ?>style="display:none;"<?php endif; ?>>
            <div class="question-image-wrapper">
                <img id="questionImage" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Valentine" class="question-image">
            </div>
            <p class="recipient-name" id="recipientName"><?php echo htmlspecialchars($recipientName); ?></p>
            <h2 class="question-text" id="questionText"><?php echo htmlspecialchars($question); ?></h2>
            <p class="sender-name">From: <span id="senderName"><?php echo htmlspecialchars($senderName); ?></span></p>
            <div class="button-section">
                <button id="yesBtn" class="answer-button btn-yes">YES! 💕</button>
                <button id="noBtn" class="answer-button btn-no">No</button>
            </div>
        </div>

        <!-- Success State -->
        <div class="success-state <?php echo $responded ? 'show' : ''; ?>" id="successState">
            <div class="success-icon">🎉</div>
            <h2 class="animate__animated animate__fadeInDown fw-bold text-white">Yay! They said YES!</h2>
            <p><span id="senderNameSuccess"><?php echo htmlspecialchars($senderName); ?></span> will be so happy!</p>
            <p>You've made their day special! ❤️</p>
            <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExOHAzeTB0OWZrZ3JnYzA4czg3Y2tieHl4ZG9sNWZvdGU4b2dqNWdjNiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/IzXiddo2twMmdmU8Lv/giphy.gif" class="success-gif" alt="Celebration">
            <div class="success-cta">
                <p>Want to create your own?</p>
                <a href="index.php" class="btn-valentine btn-primary">Create My Valentine Link</a>
            </div>
        </div>
    </div>

    <!-- Pass valentine ID to JavaScript -->
    <script>
        window.VALENTINE_ID = "<?php echo htmlspecialchars($uniqueId); ?>";
        window.API_BASE = "<?php echo BASE_URL; ?>/api";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/free-tier.js"></script>
</body>
</html>
