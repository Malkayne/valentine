<?php
session_start();
if (!isset($_SESSION['premium_user_id'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../config.php';
$userName = htmlspecialchars($_SESSION['premium_user_name']);
$baseUrl = BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Builder | Premium Valentine's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/premium-tier.css">
    <style>
        .char-counter { font-size: 0.8rem; color: #888; text-align: right; margin-top: 4px; }
        .char-counter.warning { color: #dc3545; }
        .url-status { font-size: 0.85rem; margin-top: 4px; }
        .url-status.available { color: #28a745; }
        .url-status.taken { color: #dc3545; }
        .gallery-type-switcher { display: flex; gap: 8px; margin-bottom: 12px; }
        .gallery-type-btn { padding: 8px 16px; border: 2px solid var(--medium-gray); background: #fff; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
        .gallery-type-btn.active { border-color: var(--premium-primary); background: var(--premium-light); color: var(--premium-primary); font-weight: 600; }
        .music-type-switcher { display: flex; gap: 8px; margin-bottom: 12px; }
        .music-type-btn { padding: 8px 16px; border: 2px solid var(--medium-gray); background: #fff; border-radius: 8px; cursor: pointer; transition: all 0.2s; font-size: 0.9rem; }
        .music-type-btn.active { border-color: var(--premium-primary); background: var(--premium-light); color: var(--premium-primary); font-weight: 600; }
        .music-panel { display: none; margin-top: 10px; }
        .music-panel.active { display: block; }
        .music-file-info { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: var(--light-gray); border-radius: 8px; margin-top: 8px; }
        .uploaded-gallery-caption { width: 100%; border: 1px solid var(--medium-gray); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; margin-top: 4px; }
        .note-editor { border-radius: 8px !important; }
        .builder-user-bar { display: flex; justify-content: space-between; align-items: center; padding: 8px 16px; background: var(--premium-primary); color: #fff; font-size: 0.85rem; }
        .builder-user-bar a { color: #ffd700; text-decoration: none; }
        .timeline-inline-form .form-control { margin-bottom: 6px; }
        /* Lightbox in preview */
        .preview-lightbox { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .preview-lightbox img, .preview-lightbox video { max-width: 90vw; max-height: 90vh; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="builder-user-bar">
        <span>👋 Hi, <?php echo $userName; ?>!</span>
        <a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="builder-container" id="premiumBuilder">
        <!-- Left Panel - Controls -->
        <div class="builder-panel">
            <div class="builder-header">
                <h1><i class="fas fa-heart-circle-bolt"></i> Love Website Builder</h1>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
                </div>
                <div class="progress-text" id="progressText">0% Complete</div>
            </div>

            <!-- Tabs -->
            <div class="builder-tabs">
                <button class="tab-button active" data-tab="basic"><i class="fas fa-info-circle"></i> Basic</button>
                <button class="tab-button" data-tab="design"><i class="fas fa-palette"></i> Design</button>
                <button class="tab-button" data-tab="content"><i class="fas fa-file-alt"></i> Content</button>
                <button class="tab-button" data-tab="settings"><i class="fas fa-cog"></i> Settings</button>
            </div>

            <!-- Basic Info Tab -->
            <div id="basicTab" class="tab-content active">
                <div class="tab-section">
                    <h3 class="tab-section-title">Couple Information</h3>
                    <div class="form-group">
                        <label class="form-label">Partner 1 Name *</label>
                        <input type="text" class="form-control form-control-premium" id="partner1Name" placeholder="Alex" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Partner 2 Name *</label>
                        <input type="text" class="form-control form-control-premium" id="partner2Name" placeholder="Jordan" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Anniversary Date</label>
                        <input type="date" class="form-control form-control-premium" id="anniversaryDate">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Title *</label>
                        <input type="text" class="form-control form-control-premium" id="websiteTitle" placeholder="Our Love Story" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Welcome Message</label>
                        <textarea class="form-control form-control-premium" id="welcomeMessage" rows="3" maxlength="250" placeholder="Welcome to our love story..."></textarea>
                        <div class="char-counter" id="welcomeCounter">0 / 250</div>
                    </div>
                </div>
            </div>

            <!-- Design Tab -->
            <div id="designTab" class="tab-content">
                <div class="tab-section">
                    <h3 class="tab-section-title">Choose Template</h3>
                    <div class="template-selector">
                        <div class="template-option active" data-template="romantic">
                            <div style="width:100%;height:120px;background:linear-gradient(135deg,#FF1744,#FF6B6B,#FFB6C1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;">💕</div>
                            <div class="template-option-name">Romantic</div>
                        </div>
                        <div class="template-option" data-template="elegant">
                            <div style="width:100%;height:120px;background:linear-gradient(135deg,#8B4789,#D4A5D4,#F8F4F9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;">✨</div>
                            <div class="template-option-name">Elegant</div>
                        </div>
                        <div class="template-option" data-template="modern">
                            <div style="width:100%;height:120px;background:linear-gradient(135deg,#1a1a2e,#16213e,#0f3460);display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;">💎</div>
                            <div class="template-option-name">Modern Dark</div>
                        </div>
                        <div class="template-option" data-template="classic">
                            <div style="width:100%;height:120px;background:linear-gradient(135deg,#D4AF37,#F5E6A3,#B8860B);display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;">🌹</div>
                            <div class="template-option-name">Classic Gold</div>
                        </div>
                    </div>
                </div>

                <div class="tab-section">
                    <h3 class="tab-section-title">Color Scheme</h3>
                    <div class="color-presets">
                        <div class="color-preset active" data-preset="romantic-red">
                            <div class="color-preset-preview" style="background: linear-gradient(135deg, #FF1744, #FFB6C1);"></div>
                            <div>Romantic Red</div>
                        </div>
                        <div class="color-preset" data-preset="elegant-purple">
                            <div class="color-preset-preview" style="background: linear-gradient(135deg, #8B4789, #D4A5D4);"></div>
                            <div>Elegant Purple</div>
                        </div>
                        <div class="color-preset" data-preset="soft-pink">
                            <div class="color-preset-preview" style="background: linear-gradient(135deg, #FFB6C1, #FFC0CB);"></div>
                            <div>Soft Pink</div>
                        </div>
                        <div class="color-preset" data-preset="midnight-rose">
                            <div class="color-preset-preview" style="background: linear-gradient(135deg, #1a1a2e, #e94560);"></div>
                            <div>Midnight Rose</div>
                        </div>
                    </div>
                    <div class="color-picker-group mt-3">
                        <div class="color-input-wrapper">
                            <label>Primary</label>
                            <input type="color" class="color-input" id="primaryColor" value="#FF1744">
                        </div>
                        <div class="color-input-wrapper">
                            <label>Secondary</label>
                            <input type="color" class="color-input" id="secondaryColor" value="#FFB6C1">
                        </div>
                        <div class="color-input-wrapper">
                            <label>Accent</label>
                            <input type="color" class="color-input" id="accentColor" value="#FFD700">
                        </div>
                        <div class="color-input-wrapper">
                            <label>Background</label>
                            <input type="color" class="color-input" id="bgColor" value="#FFFFFF">
                        </div>
                    </div>
                </div>

                <div class="tab-section">
                    <h3 class="tab-section-title">Font Style</h3>
                    <div class="font-selector">
                        <div class="font-option active" data-font="romantic-elegant">
                            <div class="font-preview-heading" style="font-family:'Dancing Script',cursive;">Romantic & Elegant</div>
                            <div class="font-preview-body" style="font-family:'Lora',serif;">Beautiful body text with serif style</div>
                        </div>
                        <div class="font-option" data-font="modern-clean">
                            <div class="font-preview-heading" style="font-family:'Montserrat',sans-serif;font-weight:700;">Modern & Clean</div>
                            <div class="font-preview-body" style="font-family:'Inter',sans-serif;">Clean sans-serif body text</div>
                        </div>
                        <div class="font-option" data-font="classic-serif">
                            <div class="font-preview-heading" style="font-family:'Playfair Display',serif;">Classic Serif</div>
                            <div class="font-preview-body" style="font-family:'Source Serif Pro',serif;">Traditional serif body text</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Tab -->
            <div id="contentTab" class="tab-content">
                <div class="tab-section">
                    <h3 class="tab-section-title">Our Story</h3>
                    <div class="toggle-wrapper">
                        <label>Enable Story Section</label>
                        <div class="toggle-switch">
                            <input type="checkbox" class="section-toggle" id="storyToggle" data-section="story" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div id="storyEditorContainer">
                        <textarea id="storyContent" class="form-control"></textarea>
                    </div>
                </div>

                <div class="tab-section">
                    <h3 class="tab-section-title">Gallery</h3>
                    <div class="toggle-wrapper">
                        <label>Enable Gallery</label>
                        <div class="toggle-switch">
                            <input type="checkbox" class="section-toggle" id="galleryToggle" data-section="gallery" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="gallery-type-switcher">
                        <button class="gallery-type-btn active" data-type="images"><i class="fas fa-images"></i> Photos (max 6)</button>
                        <button class="gallery-type-btn" data-type="video"><i class="fas fa-video"></i> Video (1)</button>
                    </div>
                    <div id="galleryImagesPanel">
                        <input type="file" id="galleryImageUpload" accept="image/jpeg,image/png,image/gif,image/webp" multiple style="display:none;">
                        <button class="btn-valentine btn-premium btn-sm" id="galleryUploadBtn">
                            <i class="fas fa-upload"></i> Upload Photos
                        </button>
                        <small class="text-muted d-block mt-1">Max 6 photos, 5MB each (JPG, PNG, GIF, WebP)</small>
                        <div id="galleryImagesContainer" class="image-upload-grid mt-3"></div>
                    </div>
                    <div id="galleryVideoPanel" style="display:none;">
                        <input type="file" id="galleryVideoUpload" accept="video/mp4,video/webm,video/quicktime" style="display:none;">
                        <button class="btn-valentine btn-premium btn-sm" id="videoUploadBtn">
                            <i class="fas fa-upload"></i> Upload Video
                        </button>
                        <small class="text-muted d-block mt-1">Max 1 video, 20MB (MP4, WebM, MOV)</small>
                        <div id="galleryVideoContainer" class="mt-3"></div>
                    </div>
                </div>

                <div class="tab-section">
                    <h3 class="tab-section-title">Timeline</h3>
                    <div class="toggle-wrapper">
                        <label>Enable Timeline</label>
                        <div class="toggle-switch">
                            <input type="checkbox" class="section-toggle" id="timelineToggle" data-section="timeline" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <button id="addTimelineEvent" class="btn-valentine btn-premium btn-sm">
                        <i class="fas fa-plus"></i> Add Event
                    </button>
                    <small class="text-muted d-block mt-1">Max 10 events</small>
                    <div id="timelineEventsContainer" class="timeline-items mt-3"></div>
                </div>

                <div class="tab-section">
                    <h3 class="tab-section-title">Background Music</h3>
                    <div class="music-type-switcher">
                        <button class="music-type-btn active" data-music="none"><i class="fas fa-volume-mute"></i> None</button>
                        <button class="music-type-btn" data-music="upload"><i class="fas fa-file-audio"></i> Upload</button>
                        <button class="music-type-btn" data-music="spotify"><i class="fab fa-spotify"></i> Spotify</button>
                    </div>
                    <div id="musicUploadPanel" class="music-panel">
                        <input type="file" id="musicFileUpload" accept="audio/mpeg,audio/ogg,audio/wav,audio/mp4" style="display:none;">
                        <button class="btn-valentine btn-premium btn-sm" id="musicUploadBtn">
                            <i class="fas fa-upload"></i> Upload Audio
                        </button>
                        <small class="text-muted d-block mt-1">MP3, OGG, WAV, M4A — Max 10MB</small>
                        <div id="musicFileInfo"></div>
                    </div>
                    <div id="musicSpotifyPanel" class="music-panel">
                        <div class="form-group">
                            <label class="form-label">Spotify Track URL</label>
                            <input type="url" class="form-control form-control-premium" id="spotifyUrl" placeholder="https://open.spotify.com/track/...">
                            <small class="text-muted">Paste the Spotify share link for the song</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settingsTab" class="tab-content">
                <div class="tab-section">
                    <h3 class="tab-section-title">Website Settings</h3>
                    <div class="form-group">
                        <label class="form-label">Custom URL *</label>
                        <div class="input-group">
                            <span class="input-group-text" id="urlPrefix"><?php echo rtrim($baseUrl, '/'); ?>/love/</span>
                            <input type="text" class="form-control form-control-premium" id="customUrl" placeholder="our-love-story" pattern="[a-z0-9\-]+">
                        </div>
                        <div class="url-status" id="urlStatus"></div>
                        <small class="text-muted">Only lowercase letters, numbers, and hyphens</small>
                    </div>
                    <div class="toggle-wrapper mt-3">
                        <label>Password Protection</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="passwordProtected">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div id="passwordInput" style="display: none;" class="mt-2">
                        <input type="password" class="form-control form-control-premium" id="sitePassword" placeholder="Enter a password for visitors">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Preview -->
        <div class="preview-panel">
            <div class="preview-header">
                <div class="preview-title"><i class="fas fa-eye"></i> Live Preview</div>
                <div class="preview-controls">
                    <div class="device-toggle">
                        <button class="device-btn active" data-device="desktop"><i class="fas fa-desktop"></i></button>
                        <button class="device-btn" data-device="tablet"><i class="fas fa-tablet-alt"></i></button>
                        <button class="device-btn" data-device="mobile"><i class="fas fa-mobile-alt"></i></button>
                    </div>
                </div>
            </div>
            <div id="previewFrame" class="preview-frame">
                <div style="padding: 40px; text-align: center; color: #999;">
                    <i class="fas fa-heart" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h2>Preview will appear here</h2>
                    <p>Start customizing to see your website come to life!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="action-bar-left">
            <span id="saveStatus"><i class="fas fa-save"></i> Auto-saving...</span>
        </div>
        <div class="action-bar-right">
            <button id="saveBtn" class="btn-valentine btn-outline">
                <i class="fas fa-save"></i> Save
            </button>
            <button id="publishBtn" class="btn-valentine btn-success">
                <i class="fas fa-rocket"></i> Publish
            </button>
        </div>
    </div>

    <!-- Pass base URL to JS -->
    <script>const APP_BASE_URL = '<?php echo rtrim($baseUrl, '/'); ?>';</script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/premium-tier.js"></script>
</body>
</html>
