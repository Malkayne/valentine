<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Love Website Builder | Valentine's Special</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/premium-tier.css">
</head>
<body class="premium-landing-bg">
    <div class="heart-bg"></div>
    <style>
        .premium-landing-bg {
            background: linear-gradient(135deg, #F8F4F9 0%, #D4A5D4 50%, #8B4789 100%);
            min-height: 100vh;
        }
        .premium-features {
            background: #fff;
            border-radius: 40px 40px 0 0;
            margin-top: -40px;
            position: relative;
            z-index: 10;
        }
        .premium-footer {
            background: #1a1a2e;
            color: #fff;
            padding: 60px 0;
            text-align: center;
        }
        .premium-footer .footer-logo {
            font-family: var(--font-heading);
            font-size: 2rem;
            color: var(--premium-accent);
            margin-bottom: 15px;
        }
        .premium-footer p {
            opacity: 0.7;
            font-size: 0.9rem;
        }
    </style>
    <header class="premium-header">
        <div class="container-custom">
            <div class="premium-logo">
                <a href="../" style="text-decoration:none;color:inherit;">
                    <i class="fas fa-crown"></i> Valentine's Special
                </a>
                <span class="premium-badge">PREMIUM</span>
            </div>
        </div>
    </header>
    <section class="premium-hero">
        <div class="premium-hero-icon">👑</div>
        <h1>Create a Personalized Love Story Website</h1>
        <p>The perfect Valentine's gift - A beautiful website just for the two of you</p>
        <div class="mt-4">
            <?php if (isset($_SESSION['premium_user_id'])): ?>
                <a href="builder.php" class="btn-valentine btn-premium btn-lg">
                    <i class="fas fa-rocket"></i> Go to Builder
                </a>
            <?php else: ?>
                <button class="btn-valentine btn-premium btn-lg" data-bs-toggle="modal" data-bs-target="#authModal">
                    <i class="fas fa-rocket"></i> Start Building - ₦2,500
                </button>
            <?php endif; ?>
        </div>
    </section>
    <section class="premium-features">
        <div class="container-custom">
            <h2>Everything You Need for a Perfect Love Website</h2>
            <div class="premium-features-grid">
                <div class="premium-feature-card">
                    <div class="premium-feature-icon"><i class="fas fa-images"></i></div>
                    <h3>Photo & Video Gallery</h3>
                    <p>Upload up to 6 photos or 1 video of your best memories</p>
                </div>
                <div class="premium-feature-card">
                    <div class="premium-feature-icon"><i class="fas fa-pen-fancy"></i></div>
                    <h3>Rich Story Editor</h3>
                    <p>Write your love story with beautiful formatting</p>
                </div>
                <div class="premium-feature-card">
                    <div class="premium-feature-icon"><i class="fas fa-palette"></i></div>
                    <h3>Full Customization</h3>
                    <p>Choose colors, fonts, and templates</p>
                </div>
                <div class="premium-feature-card">
                    <div class="premium-feature-icon"><i class="fas fa-music"></i></div>
                    <h3>Background Music</h3>
                    <p>Upload a song or paste a Spotify link</p>
                </div>
                <div class="premium-feature-card">
                    <div class="premium-feature-icon"><i class="fas fa-timeline"></i></div>
                    <h3>Relationship Timeline</h3>
                    <p>Showcase your journey together</p>
                </div>
                <div class="premium-feature-card">
                    <div class="premium-feature-icon"><i class="fas fa-lock"></i></div>
                    <h3>Password Protection</h3>
                    <p>Keep your love story private</p>
                </div>
            </div>
        </div>
    </section>
    <section class="pricing-section">
        <div class="container-custom">
            <div class="pricing-card">
                <h3>Premium Love Website</h3>
                <div class="price">
                    <span class="price-old">₦5,000</span>
                    <span class="price-currency">₦</span>2,500
                </div>
                <p class="price-period">One-time payment • 30 days hosting included</p>
                <div class="pricing-features">
                    <div class="pricing-feature"><i class="fas fa-check"></i> Beautiful templates & colors</div>
                    <div class="pricing-feature"><i class="fas fa-check"></i> Photo & video gallery</div>
                    <div class="pricing-feature"><i class="fas fa-check"></i> Rich text story editor</div>
                    <div class="pricing-feature"><i class="fas fa-check"></i> Background music</div>
                    <div class="pricing-feature"><i class="fas fa-check"></i> Relationship timeline</div>
                    <div class="pricing-feature"><i class="fas fa-check"></i> Password protection</div>
                    <div class="pricing-feature"><i class="fas fa-check"></i> Custom URL</div>
                </div>
                <?php if (isset($_SESSION['premium_user_id'])): ?>
                    <a href="builder.php" class="btn-valentine btn-premium btn-lg">Go to Builder</a>
                <?php else: ?>
                    <button class="btn-valentine btn-premium btn-lg" data-bs-toggle="modal" data-bs-target="#authModal">Start Building Now</button>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="premium-footer">
        <div class="container-custom">
            <div class="footer-logo">💝 Valentine's Special</div>
            <p>&copy; 2026 Valentine's Special. All rights reserved.</p>
            <p>Built with passion for lovers everywhere. <a href="https://intellicsolutions.org" target="_blank" style="color: var(--premium-accent);">Intellic Solutions Team</a>.</p>
        </div>
    </footer>

    <!-- Auth Modal -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border:none;border-radius:20px;box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                <div class="modal-header border-0" style="background:linear-gradient(135deg,#8B4789,#D4A5D4);color:#fff;padding:30px;border-top-left-radius:20px;border-top-right-radius:20px;">
                    <div style="text-align:center;width:100%;">
                        <h3 class="modal-title" id="authModalLabel" style="font-family:var(--font-romantic);font-size:2rem;margin-bottom:5px;"><i class="fas fa-heart"></i> Get Started</h3>
                        <p style="margin:0;opacity:0.9;font-size:0.9rem;">Join the premium love experience</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute;right:20px;top:20px;border-radius:50%;background-color:rgba(255,255,255,0.2);opacity:1;padding:10px;"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Tab Switcher -->
                    <style>
                        .auth-nav .nav-link {
                            color: #666;
                            font-weight: 600;
                            border-radius: 12px;
                            padding: 12px;
                            transition: all 0.3s;
                            border: 2px solid transparent;
                            margin-bottom: 5px;
                        }
                        .auth-nav .nav-link.active {
                            background: rgba(139, 71, 137, 0.1) !important;
                            color: #8B4789 !important;
                            border-color: #8B4789;
                        }
                        .auth-nav .nav-link:hover:not(.active) {
                            background: #f8f8f8;
                        }
                    </style>
                    <div class="nav nav-pills nav-justified mb-4 auth-nav" style="background:#f8f9fa;padding:5px;border-radius:15px;">
                        <li class="nav-item">
                            <button class="nav-link active w-100" id="tab-login-btn" onclick="toggleAuthTab('login')" style="border-radius:12px;">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </li>
                        <li class="nav-item ms-2">
                            <button class="nav-link w-100" id="tab-register-btn" onclick="toggleAuthTab('register')" style="border-radius:12px;">
                                <i class="fas fa-user-plus me-2"></i> Register
                            </button>
                        </li>
                    </div>

                    <div id="authError" class="alert alert-danger d-none animate__animated animate__shakeX" role="alert" style="border-radius:12px;font-size:0.9rem;"></div>

                    <!-- Manual Form Containers -->
                    <div id="loginFormSection">
                        <form id="loginForm">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight:600;color:#555;">Email Address</label>
                                <input type="email" class="form-control" id="loginEmail" required placeholder="you@email.com" style="border-radius:10px;padding:12px;border:2px solid #eee;">
                            </div>
                            <div class="mb-4">
                                <label class="form-label" style="font-weight:600;color:#555;">Secret Password</label>
                                <input type="password" class="form-control" id="loginPassword" required placeholder="••••••••" style="border-radius:10px;padding:12px;border:2px solid #eee;">
                            </div>
                            <button type="submit" class="btn w-100 text-white" style="background:linear-gradient(135deg,#8B4789,#D4A5D4);padding:14px;border-radius:12px;font-weight:600;font-size:1.1rem;box-shadow:0 4px 15px rgba(139,71,137,0.3);">
                                Login to Builder
                            </button>
                        </form>
                    </div>

                    <div id="registerFormSection" style="display:none;">
                        <form id="registerForm">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight:600;color:#555;">Full Name</label>
                                <input type="text" class="form-control" id="regName" required placeholder="Your Name" style="border-radius:10px;padding:12px;border:2px solid #eee;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-weight:600;color:#555;">Email Address</label>
                                <input type="email" class="form-control" id="regEmail" required placeholder="you@email.com" style="border-radius:10px;padding:12px;border:2px solid #eee;">
                            </div>
                            <div class="mb-4">
                                <label class="form-label" style="font-weight:600;color:#555;">Create Password (min 6 chars)</label>
                                <input type="password" class="form-control" id="regPassword" required minlength="6" placeholder="••••••••" style="border-radius:10px;padding:12px;border:2px solid #eee;">
                            </div>
                            <button type="submit" class="btn w-100 text-white" style="background:linear-gradient(135deg,#8B4789,#D4A5D4);padding:14px;border-radius:12px;font-weight:600;font-size:1.1rem;box-shadow:0 4px 15px rgba(139,71,137,0.3);">
                                Create My Website
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script>
    // Auth form handling
    const authError = document.getElementById('authError');

    function toggleAuthTab(tab) {
        const loginSection = document.getElementById('loginFormSection');
        const registerSection = document.getElementById('registerFormSection');
        const loginBtn = document.getElementById('tab-login-btn');
        const registerBtn = document.getElementById('tab-register-btn');

        if (tab === 'login') {
            loginSection.style.display = 'block';
            registerSection.style.display = 'none';
            loginBtn.classList.add('active');
            registerBtn.classList.remove('active');
        } else {
            loginSection.style.display = 'none';
            registerSection.style.display = 'block';
            loginBtn.classList.remove('active');
            registerBtn.classList.add('active');
        }
    }

    function showAuthError(msg) {
        authError.textContent = msg;
        authError.classList.remove('d-none');
    }

    function hideAuthError() {
        authError.classList.add('d-none');
    }

    document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAuthError();
        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';

        try {
            const res = await fetch('../api/auth/login.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    email: document.getElementById('loginEmail').value,
                    password: document.getElementById('loginPassword').value
                })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = 'builder.php';
            } else {
                showAuthError(data.error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
            }
        } catch (err) {
            showAuthError('Something went wrong. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
        }
    });

    document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAuthError();
        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';

        try {
            const res = await fetch('../api/auth/register.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    name: document.getElementById('regName').value,
                    email: document.getElementById('regEmail').value,
                    password: document.getElementById('regPassword').value
                })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = 'builder.php';
            } else {
                showAuthError(data.error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
            }
        } catch (err) {
            showAuthError('Something went wrong. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
        }
    });
    </script>
</body>
</html>
