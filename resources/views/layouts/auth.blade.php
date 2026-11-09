<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KasKita – Masuk / Daftar')</title>
    <!-- Favicon / Web Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.svg') }}">

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #198754;
            --primary-dark: #146c43;
            --primary-light: #20c997;
            --primary-subtle: #e8f5e9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(25, 135, 84, 0.08) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(32, 201, 151, 0.1) 0%, transparent 40%),
                        #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient floating circles in background */
        .ambient-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
            animation: floatAmbient 12s ease-in-out infinite alternate;
        }
        .ambient-circle-1 {
            width: 320px;
            height: 320px;
            background: rgba(25, 135, 84, 0.12);
            top: -50px;
            right: 5%;
        }
        .ambient-circle-2 {
            width: 280px;
            height: 280px;
            background: rgba(32, 201, 151, 0.15);
            bottom: -50px;
            left: 5%;
            animation-duration: 15s;
        }

        @keyframes floatAmbient {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(35px) rotate(20deg); }
        }

        /* Entrance Animations */
        .animate-brand {
            animation: fadeDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .animate-card {
            animation: cardZoomUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) both 0.15s;
        }
        .animate-footer {
            animation: fadeIn 0.8s ease both 0.4s;
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes cardZoomUp {
            from {
                opacity: 0;
                transform: translateY(35px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Logo pulse icon */
        .logo-icon-pulse {
            position: relative;
            animation: pulseGlow 3s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
                transform: scale(1.05);
            }
        }

        .auth-card {
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 45px rgba(15, 81, 50, 0.08);
            width: 100%;
            position: relative;
            overflow: hidden;
            z-index: 1;
            transition: box-shadow 0.3s ease;
        }
        .auth-card:hover {
            box-shadow: 0 25px 50px rgba(15, 81, 50, 0.12);
        }

        /* Card Top Loading Progress Line */
        .card-progress-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #198754, #20c997, #198754);
            background-size: 200% 100%;
            display: none;
            animation: shimmerBar 1.2s infinite linear;
        }
        @keyframes shimmerBar {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .btn-kaskita {
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            border: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-kaskita:hover {
            background-color: var(--primary-dark);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3);
            transform: translateY(-2px);
        }
        .btn-kaskita:active {
            transform: translateY(0);
        }

        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            transition: all 0.25s ease;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.15);
            transform: translateY(-1px);
        }

        .input-group-text {
            border-radius: 12px;
            transition: all 0.25s ease;
        }
        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            background-color: var(--primary-subtle);
            color: var(--primary);
        }

        .brand-link {
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.7rem;
            transition: transform 0.3s ease;
        }
        .brand-link:hover {
            transform: scale(1.03);
            color: var(--primary-dark);
        }

        /* Error alert shake */
        .shake-alert {
            animation: alertShake 0.5s ease-in-out both;
        }
        @keyframes alertShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        /* Transition overlay upon successful submission */
        #authOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        #authOverlay.active {
            display: flex;
            opacity: 1;
        }
        .spinner-custom {
            width: 48px;
            height: 48px;
            border: 4px solid var(--primary-subtle);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Ambient background glows -->
    <div class="ambient-circle ambient-circle-1"></div>
    <div class="ambient-circle ambient-circle-2"></div>

    <!-- Processing Overlay upon Login Submit -->
    <div id="authOverlay">
        <div class="spinner-custom mb-3"></div>
        <h5 class="fw-bold text-dark mb-1">Memverifikasi Akun KasKita...</h5>
        <p class="text-secondary small mb-0">Menyiapkan buku kas & keuangan usaha Anda</p>
    </div>

    <div class="container position-relative z-1">
        @yield('content')
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>