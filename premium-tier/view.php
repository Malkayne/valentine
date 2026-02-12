<?php
// ================================================
// PREMIUM VIEW PAGE — Renders the love story website
// ================================================
session_start();
require_once __DIR__ . '/../config.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    http_response_code(404);
    echo '<h1 style="text-align:center;margin-top:100px;">Love page not found 💔</h1>';
    exit;
}

// Load website data
$stmt = mysqli_prepare($database, "SELECT * FROM premium_websites WHERE custom_url = ? AND is_published = 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$site = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$site) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head><body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f8f4f9;">
    <div style="text-align:center;"><h1 style="font-size:4rem;">💔</h1>
    <h2>Love page not found</h2><p>This page may not exist or hasn\'t been published yet.</p>
    <a href="' . BASE_URL . '" class="btn btn-outline-secondary mt-3">Go Home</a></div></body></html>';
    exit;
}

// Password protection check
if ($site['password_protected'] && $site['site_password']) {
    $authenticated = isset($_SESSION['site_auth_' . $site['id']]) && $_SESSION['site_auth_' . $site['id']] === true;

    if (!$authenticated) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['site_password'])) {
            if (password_verify($_POST['site_password'], $site['site_password'])) {
                $_SESSION['site_auth_' . $site['id']] = true;
                // Continue to render page
            } else {
                $passwordError = 'Incorrect password. Try again.';
                // Show password form
                renderPasswordForm($site, $passwordError ?? null);
                exit;
            }
        } else {
            renderPasswordForm($site, null);
            exit;
        }
    }
}

// Decode JSON fields
$galleryData  = json_decode($site['gallery_data'], true) ?: [];
$timelineData = json_decode($site['timeline_data'], true) ?: [];

// Template colors
$primary   = htmlspecialchars($site['primary_color']);
$secondary = htmlspecialchars($site['secondary_color']);
$accent    = htmlspecialchars($site['accent_color']);
$bgColor   = htmlspecialchars($site['bg_color']);

// Font family
$fontMap = [
    'romantic-elegant' => ["'Dancing Script', cursive", "'Lora', serif"],
    'modern-clean'     => ["'Montserrat', sans-serif", "'Inter', sans-serif"],
    'classic-serif'    => ["'Playfair Display', serif", "'Source Serif Pro', serif"]
];
$fontPair = $fontMap[$site['font_pairing']] ?? $fontMap['romantic-elegant'];

$partner1 = htmlspecialchars($site['partner1_name']);
$partner2 = htmlspecialchars($site['partner2_name']);
$title    = htmlspecialchars($site['website_title']);
$welcome  = htmlspecialchars($site['welcome_message']);
$baseUrl  = BASE_URL;

function renderPasswordForm($site, $error) {
    $title = htmlspecialchars($site['website_title'] ?: 'Love Story');
    $p1 = htmlspecialchars($site['partner1_name']);
    $p2 = htmlspecialchars($site['partner2_name']);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>' . $title . '</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:linear-gradient(135deg,' . $site['primary_color'] . '33,' . $site['secondary_color'] . '33);font-family:sans-serif;}
    .lock-card{background:#fff;border-radius:20px;padding:40px;max-width:400px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.1);}
    .lock-card h2{margin:15px 0 5px;font-size:1.6rem;color:#333;} .lock-card p{color:#666;margin-bottom:20px;}
    .lock-icon{font-size:3rem;color:' . $site['primary_color'] . ';}</style></head>
    <body><div class="lock-card"><div class="lock-icon"><i class="fas fa-lock"></i></div>
    <h2>' . $p1 . ' & ' . $p2 . '</h2><p>This love story is password protected</p>';
    if ($error) echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
    echo '<form method="POST"><div class="mb-3"><input type="password" class="form-control" name="site_password" placeholder="Enter password" required autofocus></div>
    <button type="submit" class="btn w-100 text-white" style="background:' . $site['primary_color'] . ';padding:12px;border-radius:10px;">
    <i class="fas fa-unlock"></i> Unlock</button></form></div></body></html>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> | <?php echo $partner1; ?> & <?php echo $partner2; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Lora:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;600;700&family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Source+Serif+Pro:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/premium-tier.css">
    <style>
        :root {
            --site-primary: <?php echo $primary; ?>;
            --site-secondary: <?php echo $secondary; ?>;
            --site-accent: <?php echo $accent; ?>;
            --site-bg: <?php echo $bgColor; ?>;
        }
        body { font-family: <?php echo $fontPair[1]; ?>; color: #333; }
        h1, h2, h3 { font-family: <?php echo $fontPair[0]; ?>; }

        .premium-hero-section { background: linear-gradient(135deg, var(--site-primary), var(--site-secondary)); }
        .premium-hero-content h1 { font-family: <?php echo $fontPair[0]; ?>; }
        .welcome-content h2, .story-content h2, .gallery-section h2,
        .timeline-section h2, .final-message-content h2 { color: var(--site-primary); }
        .couple-photo { border-color: var(--site-primary); }
        .timeline::before { background: var(--site-primary); }
        .timeline-marker { background: var(--site-primary); }
        .timeline-date { color: var(--site-primary); }
        .final-message-section { background: linear-gradient(135deg, var(--site-primary), var(--site-secondary)); }
        .story-section { background: var(--site-bg); }
        .music-player-btn { background: var(--site-primary); }
        .music-player-btn:hover { background: var(--site-accent); }

        /* Lightbox */
        .lightbox-overlay { position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.92);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer;opacity:0;pointer-events:none;transition:opacity 0.3s; }
        .lightbox-overlay.active { opacity:1;pointer-events:auto; }
        .lightbox-overlay img, .lightbox-overlay video { max-width:90vw;max-height:85vh;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.5); }
        .lightbox-close { position:absolute;top:20px;right:30px;color:#fff;font-size:2rem;cursor:pointer;z-index:10000; }
        .lightbox-caption { position:absolute;bottom:30px;left:50%;transform:translateX(-50%);color:#fff;font-size:1.1rem;text-shadow:1px 1px 4px rgba(0,0,0,0.7); }

        .gallery-item { cursor: pointer; }
        .gallery-item .play-overlay { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:3rem;color:#fff;text-shadow:0 2px 10px rgba(0,0,0,0.5); }
    </style>
</head>
<body class="premium-site">

    <!-- Hero Section -->
    <section class="premium-hero-section">
        <div class="premium-hero-overlay"></div>
        <div class="premium-hero-content" data-aos="fade-up">
            <h1><?php echo $partner1; ?> & <?php echo $partner2; ?></h1>
            <p><?php echo $title; ?></p>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Welcome Section -->
    <?php if ($welcome): ?>
    <section class="welcome-section" data-aos="fade-up">
        <div class="welcome-content">
            <h2>Welcome to Our Story</h2>
            <p class="welcome-text"><?php echo nl2br($welcome); ?></p>
        </div>
    </section>
    <?php endif; ?>

    <!-- Story Section -->
    <?php if ($site['story_enabled'] && $site['story_content']): ?>
    <section class="story-section" data-aos="fade-up">
        <div class="story-content">
            <h2>Our Story</h2>
            <div class="story-text"><?php echo $site['story_content']; ?></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Gallery Section -->
    <?php if ($site['gallery_enabled'] && !empty($galleryData)): ?>
    <section class="gallery-section" data-aos="fade-up">
        <h2>Our Memories Together</h2>
        <div class="gallery-grid">
            <?php foreach ($galleryData as $i => $item):
                $isVideo = isset($item['type']) && $item['type'] === 'video';
                $url = $baseUrl . '/' . $item['path'];
                $caption = htmlspecialchars($item['caption'] ?? '');
                $delay = $i * 100;
            ?>
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>"
                     onclick="openLightbox('<?php echo $url; ?>', '<?php echo $caption; ?>', <?php echo $isVideo ? 'true' : 'false'; ?>)">
                    <?php if ($isVideo): ?>
                        <video muted style="width:100%;height:100%;object-fit:cover;"><source src="<?php echo $url; ?>"></video>
                        <div class="play-overlay"><i class="fas fa-play-circle"></i></div>
                    <?php else: ?>
                        <img src="<?php echo $url; ?>" alt="<?php echo $caption; ?>">
                    <?php endif; ?>
                    <?php if ($caption): ?>
                        <div class="gallery-caption"><?php echo $caption; ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Timeline Section -->
    <?php if ($site['timeline_enabled'] && !empty($timelineData)): ?>
    <section class="timeline-section" data-aos="fade-up">
        <h2>Our Journey</h2>
        <div class="timeline">
            <?php foreach ($timelineData as $i => $event):
                $direction = $i % 2 === 0 ? 'fade-right' : 'fade-left';
            ?>
                <div class="timeline-event" data-aos="<?php echo $direction; ?>">
                    <div class="timeline-content">
                        <div class="timeline-date"><?php echo htmlspecialchars($event['date'] ?? ''); ?></div>
                        <div class="timeline-title"><?php echo htmlspecialchars($event['title'] ?? ''); ?></div>
                        <p class="timeline-description"><?php echo htmlspecialchars($event['description'] ?? ''); ?></p>
                    </div>
                    <div class="timeline-marker"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Final Message Section -->
    <section class="final-message-section">
        <div class="final-message-content" data-aos="fade-up">
            <h2>Forever & Always</h2>
            <p class="final-message-text">
                Happy Valentine's Day, <?php echo $partner2; ?>! ❤️<br>
                All my love, <?php echo $partner1; ?>
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="premium-footer">
        <div class="premium-footer-content">
            <p>Made with ❤️ for <?php echo $partner1; ?> & <?php echo $partner2; ?></p>
            <?php if ($site['anniversary_date']): ?>
                <p>Together since <?php echo date('F j, Y', strtotime($site['anniversary_date'])); ?></p>
            <?php endif; ?>
            <div class="premium-footer-cta">
                <a href="<?php echo $baseUrl; ?>" class="btn-valentine btn-outline">Create Your Own Love Website</a>
            </div>
        </div>
    </footer>

    <!-- Music Player -->
    <?php if ($site['music_type'] !== 'none'): ?>
    <div class="music-player" id="musicPlayer">
        <button class="music-player-btn pulse-animation" id="musicToggleBtn">
            <i class="fas fa-play"></i>
        </button>
        <div class="music-info">
            <div class="music-title">Our Song</div>
            <div class="music-artist" id="musicStatus">♫ Click to play</div>
        </div>
    </div>

    <?php if ($site['music_type'] === 'upload' && $site['music_file']): ?>
        <audio id="bgAudio" loop preload="auto">
            <source src="<?php echo $baseUrl . '/' . htmlspecialchars($site['music_file']); ?>">
        </audio>
    <?php endif; ?>

    <?php if ($site['music_type'] === 'spotify' && $site['music_spotify_url']):
        $spotifyUrl = $site['music_spotify_url'];
        preg_match('/track\/([a-zA-Z0-9]+)/', $spotifyUrl, $matches);
        $trackId = $matches[1] ?? '';
        if ($trackId):
    ?>
        <div id="spotifyEmbed" style="position:fixed;bottom:90px;right:20px;z-index:999;display:none;width:300px;background:#fff;padding:10px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.15);border:2px solid var(--site-primary);">
            <iframe src="https://open.spotify.com/embed/track/<?php echo $trackId; ?>?utm_source=generator&theme=0"
                    width="100%" height="80" frameBorder="0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    style="border-radius:12px;"></iframe>
            <div style="font-size:0.75rem;color:#888;text-align:center;margin-top:5px;">If it doesn't play, click the Spotify logo</div>
        </div>
    <?php endif; endif; ?>
    <?php endif; ?>

    <!-- Lightbox -->
    <div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
        <span class="lightbox-close"><i class="fas fa-times"></i></span>
        <div id="lightboxContent"></div>
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>

    <style>
        .pulse-animation {
            animation: pulse-music 2s infinite;
        }
        @keyframes pulse-music {
            0% { box-shadow: 0 0 0 0 <?php echo $primary; ?>77; }
            70% { box-shadow: 0 0 0 15px <?php echo $primary; ?>00; }
            100% { box-shadow: 0 0 0 0 <?php echo $primary; ?>00; }
        }
        .music-player-btn i { transition: all 0.3s ease; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({ duration: 1000, once: true });

        // Lightbox
        function openLightbox(url, caption, isVideo) {
            const content = document.getElementById('lightboxContent');
            const captionEl = document.getElementById('lightboxCaption');

            if (isVideo) {
                content.innerHTML = `<video controls autoplay style="max-width:90vw;max-height:85vh;border-radius:12px;"><source src="${url}"></video>`;
            } else {
                content.innerHTML = `<img src="${url}" alt="${caption}">`;
            }
            captionEl.textContent = caption;
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            const content = document.getElementById('lightboxContent');
            if (content.querySelector('video')) {
                content.querySelector('video').pause();
            }
            content.innerHTML = '';
            document.body.style.overflow = '';
        }

        // Close with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });

        // Music Player
        const musicToggleBtn = document.getElementById('musicToggleBtn');
        const bgAudio = document.getElementById('bgAudio');
        const spotifyEmbed = document.getElementById('spotifyEmbed');
        const musicStatus = document.getElementById('musicStatus');
        let isPlaying = false;

        if (musicToggleBtn) {
            const startPlaying = () => {
                if (bgAudio) {
                    bgAudio.play().then(() => {
                        isPlaying = true;
                        musicToggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
                        musicToggleBtn.classList.remove('pulse-animation');
                        musicStatus.textContent = '♫ Now playing';
                    }).catch(err => console.log('Autoplay blocked:', err));
                } else if (spotifyEmbed) {
                    spotifyEmbed.style.display = 'block';
                    musicToggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    musicToggleBtn.classList.remove('pulse-animation');
                    musicStatus.textContent = '♫ Opening Spotify';
                    isPlaying = true;
                }
            };

            musicToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (bgAudio) {
                    if (isPlaying) {
                        bgAudio.pause();
                        musicToggleBtn.innerHTML = '<i class="fas fa-play"></i>';
                        musicToggleBtn.classList.add('pulse-animation');
                        musicStatus.textContent = '♫ Click to play';
                    } else {
                        bgAudio.play();
                        musicToggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
                        musicToggleBtn.classList.remove('pulse-animation');
                        musicStatus.textContent = '♫ Now playing';
                    }
                    isPlaying = !isPlaying;
                } else if (spotifyEmbed) {
                    const isVisible = spotifyEmbed.style.display !== 'none';
                    spotifyEmbed.style.display = isVisible ? 'none' : 'block';
                    musicToggleBtn.innerHTML = isVisible ? '<i class="fas fa-play"></i>' : '<i class="fas fa-pause"></i>';
                    if (!isVisible) musicToggleBtn.classList.remove('pulse-animation');
                    else musicToggleBtn.classList.add('pulse-animation');
                    musicStatus.textContent = isVisible ? '♫ Click to play' : '♫ Now playing';
                    isPlaying = !isVisible;
                }
            });

            const autoPlayHandler = () => {
                if (!isPlaying) {
                    startPlaying();
                }
                document.removeEventListener('click', autoPlayHandler);
                document.removeEventListener('touchstart', autoPlayHandler);
                window.removeEventListener('scroll', autoPlayHandler);
            };

            document.addEventListener('click', autoPlayHandler);
            document.addEventListener('touchstart', autoPlayHandler);
            window.addEventListener('scroll', autoPlayHandler, { passive: true });
        }
    </script>
</body>
</html>
