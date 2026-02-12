<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentine's Special - Create Your Perfect Valentine Message</title>
    <meta name="description"
        content="Create fun Valentine proposals or beautiful romantic websites for your loved ones">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/common.css">

    <style>
        .main-landing {
            min-height: 100vh;
            background: linear-gradient(135deg, #FFE4E1 0%, #E6D5E8 100%);
            position: relative;
            overflow: hidden;
        }

        .split-container {
            min-height: 100vh;
            display: flex;
        }

        .split-half {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            transition: all 0.5s ease;
        }

        .split-half:hover {
            flex: 1.1;
        }

        .free-half {
            background: linear-gradient(135deg, #FFE4E1 0%, #FFB6C1 50%, #FF69B4 100%);
        }

        .premium-half {
            background: linear-gradient(135deg, #F8F4F9 0%, #D4A5D4 50%, #8B4789 100%);
        }

        .tier-content {
            max-width: 500px;
            text-align: center;
            color: white;
            z-index: 10;
        }

        .tier-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        .tier-title {
            font-family: 'Pacifico', cursive;
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .tier-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .tier-features {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
            text-align: left;
        }

        .tier-features li {
            padding: 0.5rem 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tier-features i {
            color: #FFD700;
            font-size: 1.2rem;
        }

        .tier-btn {
            padding: 18px 48px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .tier-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .free-btn {
            background: white;
            color: #FF1744;
        }

        .premium-btn {
            background: #FFD700;
            color: #8B4789;
        }

        @media (max-width: 992px) {
            .split-container {
                flex-direction: column;
            }

            .split-half {
                min-height: 50vh;
            }

            .tier-title {
                font-size: 2rem;
            }

            .tier-subtitle {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="heart-bg"></div>

    <div class="main-landing">
        <div class="split-container">
            <!-- Free Tier -->
            <div class="split-half free-half animate__animated animate__fadeInLeft">
                <div class="tier-content">
                    <div class="tier-icon">
                        <!-- <i class="fas fa-heart-circle-question"></i> -->
                        <i class="fas fa-heart"></i>
                    </div>
                    <h1 class="tier-title">Free Fun</h1>
                    <p class="tier-subtitle">Will You Be My Valentine?</p>

                    <ul class="tier-features">
                        <li><i class="fas fa-check-circle"></i> Interactive Question Page</li>
                        <li><i class="fas fa-check-circle"></i> Impossible to Say No!</li>
                        <li><i class="fas fa-check-circle"></i> Email Notifications</li>
                        <li><i class="fas fa-check-circle"></i> 100% Free Forever</li>
                    </ul>

                    <a href="free-tier/index.php" class="tier-btn free-btn">
                        <i class="fas fa-heart"></i> Get Started Free
                    </a>
                </div>
            </div>

            <!-- Premium Tier -->
            <div class="split-half premium-half animate__animated animate__fadeInRight">
                <div class="tier-content">
                    <div class="tier-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h1 class="tier-title">Premium Love</h1>
                    <p class="tier-subtitle">Build Your Love Story Website</p>

                    <ul class="tier-features">
                        <li><i class="fas fa-check-circle"></i> Fully Customizable</li>
                        <li><i class="fas fa-check-circle"></i> Photo & Video Galleries</li>
                        <li><i class="fas fa-check-circle"></i> Love Letter Editor</li>
                        <li><i class="fas fa-check-circle"></i> Only ₦2,500</li>
                    </ul>

                    <a href="premium-tier/index.html" class="tier-btn premium-btn">
                        <i class="fas fa-gem"></i> Start Building - ₦2,500
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Common JS -->
    <script src="assets/js/common.js"></script>
</body>

</html>