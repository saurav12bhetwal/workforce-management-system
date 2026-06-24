<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Workforce Management') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-wrapper {
            width: 100%;
            max-width: 1200px;
        }

        /* ===== NAVBAR ===== */
        .welcome-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1a1a2e;
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
        }

        .brand-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand-text span {
            color: #0d6efd;
        }

        .nav-links {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links .btn {
            padding: 10px 24px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .nav-links .btn-outline-dark {
            border: 2px solid #e9ecef;
            color: #1a1a2e;
        }

        .nav-links .btn-outline-dark:hover {
            background: #1a1a2e;
            color: #fff;
            border-color: #1a1a2e;
        }

        .nav-links .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .nav-links .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(13, 110, 253, 0.4);
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            background: #fff;
            border-radius: 24px;
            padding: 60px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }

        .hero-content h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            color: #1a1a2e;
            margin-bottom: 20px;
        }

        .hero-content h1 span {
            background: linear-gradient(135deg, #0d6efd, #6c5ce7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 18px;
            color: #6c757d;
            line-height: 1.8;
            margin-bottom: 30px;
            max-width: 500px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .hero-buttons .btn {
            padding: 14px 35px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .hero-buttons .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .hero-buttons .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(13, 110, 253, 0.4);
        }

        .hero-buttons .btn-outline-primary {
            border: 2px solid #0d6efd;
            color: #0d6efd;
        }

        .hero-buttons .btn-outline-primary:hover {
            background: #0d6efd;
            color: #fff;
        }

        /* ===== HERO ILLUSTRATION ===== */
        .hero-illustration {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .illustration-box {
            width: 100%;
            max-width: 450px;
            background: linear-gradient(135deg, #e8f0fe, #d4e4ff);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .illustration-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.05), transparent);
            border-radius: 50%;
        }

        .illustration-box .icon-circle {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #0d6efd, #6c5ce7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 45px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.3);
            position: relative;
            z-index: 1;
        }

        .illustration-box h4 {
            font-weight: 700;
            color: #1a1a2e;
            position: relative;
            z-index: 1;
        }

        .illustration-box p {
            color: #6c757d;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .floating-badges {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .floating-badges .badge-item {
            background: #fff;
            padding: 10px 18px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #1a1a2e;
            border: 1px solid #f0f0f0;
        }

        .floating-badges .badge-item i {
            color: #0d6efd;
            font-size: 16px;
        }

        /* ===== FEATURES SECTION ===== */
        .features-section {
            margin-top: 60px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .feature-card {
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-color: #0d6efd;
        }

        .feature-card .icon {
            width: 55px;
            height: 55px;
            background: #e8f0fe;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: #0d6efd;
        }

        .feature-card h6 {
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .feature-card p {
            font-size: 13px;
            color: #6c757d;
            margin: 0;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .hero-section {
                grid-template-columns: 1fr;
                padding: 40px 30px;
                gap: 30px;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .hero-content p {
                max-width: 100%;
            }

            .hero-illustration {
                order: -1;
            }

            .illustration-box {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .welcome-nav {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .brand {
                justify-content: center;
            }

            .nav-links {
                justify-content: center;
            }

            .hero-section {
                padding: 30px 20px;
            }

            .hero-content h1 {
                font-size: 28px;
            }

            .hero-content p {
                font-size: 16px;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-buttons .btn {
                width: 100%;
                text-align: center;
            }

            .features-section {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 20px 15px;
            }

            .hero-content h1 {
                font-size: 24px;
            }

            .nav-links .btn {
                padding: 8px 16px;
                font-size: 13px;
            }

            .features-section {
                grid-template-columns: 1fr;
            }

            .floating-badges .badge-item {
                font-size: 12px;
                padding: 8px 14px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-wrapper">
        <!-- ===== NAVBAR ===== -->
        <nav class="welcome-nav">
            <a href="/" class="brand">
                <div class="brand-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="brand-text">
                    Work<span>Flow</span>
                </div>
            </a>

            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark">
                            <i class="fas fa-sign-in-alt me-2"></i> Log In
                        </a>
                        {{-- @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i> Register
                            </a>
                        @endif --}}
                    @endauth
                @endif
            </div>
        </nav>

        <!-- ===== HERO SECTION ===== -->
        <section class="hero-section">
            <div class="hero-content">
                <h1>
                    Workforce Management<br>
                    <span>Made Simple</span>
                </h1>
                <p>
                    Track attendance, manage leaves, and monitor your team's productivity 
                    with our all-in-one workforce management system.
                </p>
                <div class="hero-buttons">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                        </a>
                    @else
                        {{-- <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-rocket me-2"></i> Get Started Free
                        </a> --}}
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                        </a>
                    @endauth
                </div>
            </div>

            <div class="hero-illustration">
                <div class="illustration-box">
                    <div class="icon-circle">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Smart Workforce Management</h4>
                    <p>Real-time attendance tracking & leave management</p>

                    <div class="floating-badges">
                        <div class="badge-item">
                            <i class="fas fa-clock"></i>
                            <span>Live Tracking</span>
                        </div>
                        <div class="badge-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Location</span>
                        </div>
                        <div class="badge-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Leave Mgmt</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FEATURES SECTION ===== -->
        <section class="features-section">
            <div class="feature-card">
                <div class="icon">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <h6>Smart Attendance</h6>
                <p>Check-in/out with live location tracking</p>
            </div>

            <div class="feature-card">
                <div class="icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h6>Role Management</h6>
                <p>Admin, Manager & Employee hierarchy</p>
            </div>

            <div class="feature-card">
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h6>Leave Management</h6>
                <p>Apply & approve leave requests easily</p>
            </div>

            <div class="feature-card">
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h6>Analytics Dashboard</h6>
                <p>Real-time insights & reports</p>
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>