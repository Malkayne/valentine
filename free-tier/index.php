<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Valentine Question | Free</title>
    <meta name="description" content="Create a fun, interactive Valentine's proposal they can't refuse! Free forever.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/free-tier.css">
</head>

<body class="free-tier-bg">
    <div class="heart-bg"></div>
    <header class="free-header">
        <div class="container-custom">
            <a href="../" class="free-logo"><i class="fas fa-heart"></i> Valentine's Special</a>
        </div>
    </header>
    <section class="free-hero">
        <div class="free-hero-icon">💝</div>
        <h1 class="animate__animated animate__fadeInDown">Ask That Special Question</h1>
        <p class="animate__animated animate__fadeInUp">Create a fun, interactive Valentine's proposal they can't refuse!</p>
    </section>
    <section class="creation-section">
        <div class="container-custom">
            <div class="creation-card animate__animated animate__zoomIn">
                <h2>Create Your Link</h2>
                <form id="valentineCreatorForm" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3 class="form-section-title"><i class="fas fa-user-heart"></i> Basic Information</h3>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label">Your Name *</label>
                                <input type="text" class="form-control" id="senderName" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Their Name *</label>
                                <input type="text" class="form-control" id="recipientName" placeholder="Jane Smith" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Your Email (to get notified when they say YES!)</label>
                            <input type="email" class="form-control" id="senderEmail" placeholder="you@example.com">
                        </div>
                    </div>
                    <div class="form-section">
                        <h3 class="form-section-title"><i class="fas fa-comments"></i> Choose Your Question</h3>
                        <div class="question-select-group">
                            <div class="question-option active" data-question="Will you be my Valentine?">
                                <i class="fas fa-heart"></i>
                                <div>Will you be my Valentine?</div>
                            </div>
                            <div class="question-option" data-question="Will you go out with me?">
                                <i class="fas fa-calendar-heart"></i>
                                <div>Will you go out with me?</div>
                            </div>
                            <div class="question-option" data-question="Be mine this Valentine?">
                                <i class="fas fa-gift-heart"></i>
                                <div>Be mine this Valentine?</div>
                            </div>
                            <div class="question-option" data-question="custom">
                                <i class="fas fa-pen"></i>
                                <div>Custom Question</div>
                            </div>
                        </div>
                        <input type="text" class="form-control" id="customQuestion"
                            placeholder="Write your own question..." style="display: none;">
                    </div>
                    <div class="form-section">
                        <h3 class="form-section-title"><i class="fas fa-image"></i> Add a Picture (Optional)</h3>
                        <div class="file-upload-wrapper">
                            <input type="file" id="imageUpload" class="file-upload-input" accept="image/*">
                            <label for="imageUpload" class="file-upload-label">
                                <div><i class="fas fa-cloud-upload-alt"></i>
                                    <div>Click to upload</div><small>PNG, JPG up to 5MB</small>
                                </div>
                            </label>
                        </div>
                        <div id="imagePreview" class="image-preview" style="display: none;"></div>
                    </div>
                    <div class="form-section">
                        <h3 class="form-section-title"><i class="fas fa-palette"></i> Choose Theme Color</h3>
                        <div class="theme-picker">
                            <div class="theme-option theme-romantic-red active" data-theme="theme-romantic-red"></div>
                            <div class="theme-option theme-soft-pink" data-theme="theme-soft-pink"></div>
                            <div class="theme-option theme-purple-love" data-theme="theme-purple-love"></div>
                            <div class="theme-option theme-rose-gold" data-theme="theme-rose-gold"></div>
                            <div class="theme-option theme-coral-passion" data-theme="theme-coral-passion"></div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" id="generateBtn" class="btn-valentine btn-primary btn-lg">
                            <i class="fas fa-magic"></i> Generate My Link
                        </button>
                    </div>
                </form>
                <div id="linkGenerated" class="link-generated">
                    <h3><i class="fas fa-check-circle"></i> Your Link is Ready!</h3>
                    <div class="link-display">
                        <input type="text" id="generatedLink" class="link-input" readonly>
                        <button id="copyBtn" class="btn-copy"><i class="fas fa-copy"></i> Copy</button>
                    </div>
                    <p class="text-center mb-2"><strong>Share your link:</strong></p>
                    <div class="share-buttons">
                        <button class="btn-share btn-whatsapp" data-platform="whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</button>
                        <button class="btn-share btn-facebook" data-platform="facebook"><i class="fab fa-facebook"></i> Facebook</button>
                        <button class="btn-share btn-twitter" data-platform="twitter"><i class="fab fa-twitter"></i> Twitter</button>
                        <button class="btn-share btn-email" data-platform="email"><i class="fas fa-envelope"></i> Email</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="features-section">
        <div class="container-custom">
            <h2 class="text-center mb-5"
                style="font-family: var(--font-romantic); font-size: 2.5rem; color: var(--free-primary);">Why Use Our
                Valentine Creator?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-ban"></i></div>
                    <h3>Impossible to Say No!</h3>
                    <p>The NO button moves away. They'll have to say YES!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-heart-pulse"></i></div>
                    <h3>Beautiful Animations</h3>
                    <p>Gorgeous animations and confetti!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-bell"></i></div>
                    <h3>Get Notified</h3>
                    <p>Email notification when they accept!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-gift"></i></div>
                    <h3>100% Free</h3>
                    <p>Unlimited links at no cost!</p>
                </div>
            </div>
        </div>
    </section>
    <footer class="free-footer">
        <div class="footer-content">
            <a href="../" class="footer-logo">💝 Valentine's Special</a>
            <div class="footer-links">
                <a href="../">Home</a>
                <a href="../premium-tier/">Premium</a>
            </div>
            <p class="footer-copyright">&copy; 2026 Valentine's Special. Made with ❤️ from <a href="https://intellicsolutions.org" target="_blank" style="color: var(--free-primary);">Intellic Solutions Team</a>.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/free-tier.js"></script>
</body>

</html>
