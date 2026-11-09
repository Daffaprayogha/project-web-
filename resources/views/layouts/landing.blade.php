<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KasKita – Sistem Pencatatan Kas dan Keuangan UMKM')</title>
    <!-- Favicon / Web Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.svg') }}">

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            --income: #198754;
            --expense: #dc3545;
            --info: #0d6efd;
            --dark: #212529;
            --gray-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #334155;
            background-color: #ffffff;
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #198754;
            border-radius: 4px;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link {
            font-weight: 500;
            color: #475569 !important;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
        }
        .btn-kaskita {
            background-color: var(--primary);
            color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
            border: 1px solid var(--primary);
            transition: all 0.3s ease;
        }
        .btn-kaskita:hover {
            background-color: var(--primary-dark);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
            transform: translateY(-2px);
        }
        .btn-kaskita-outline {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 8px;
            font-weight: 600;
            padding: 0.55rem 1.4rem;
            transition: all 0.3s ease;
        }
        .btn-kaskita-outline:hover {
            background-color: var(--primary);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .hero-section {
            padding: 120px 0 80px;
            background: radial-gradient(circle at top right, rgba(25, 135, 84, 0.08), transparent 50%),
                        radial-gradient(circle at bottom left, rgba(32, 201, 151, 0.08), transparent 50%);
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.25;
            color: #0f172a;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            line-height: 1.7;
            color: #64748b;
        }

        .section-title {
            font-size: 2.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }
        .section-subtitle {
            color: #64748b;
            font-size: 1.05rem;
            margin-bottom: 3rem;
        }

        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2rem 1.75rem;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(25, 135, 84, 0.3);
            box-shadow: 0 16px 32px rgba(25, 135, 84, 0.08);
        }
        .feature-icon-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
            background-color: var(--primary-subtle);
            color: var(--primary);
        }

        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #e2e8f0;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }
        .stat-number {
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--primary);
        }

        .article-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        }
        .article-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .testimonial-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2rem;
            background: #ffffff;
            height: 100%;
            position: relative;
        }

        .cta-box {
            background: linear-gradient(135deg, #198754 0%, #0f5132 100%);
            border-radius: 24px;
            padding: 4rem 2rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        footer {
            background-color: #0b1e16;
            color: #94a3b8;
            padding: 60px 0 30px;
        }
        footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        footer a:hover {
            color: #20c997;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header & Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm py-3" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="KasKita Logo" style="width: 38px; height: 38px; object-fit: contain;" class="rounded-3 shadow-sm">
                <span>KasKita</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#informasi">Informasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-kaskita">
                            <i class="bi bi-speedometer2 me-1"></i> Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-kaskita-outline">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-kaskita">Daftar Akun</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="container" style="margin-top: 90px; margin-bottom: -70px;">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer id="kontak">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bg-success text-white rounded-3 p-2 d-inline-flex">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </span>
                        <h4 class="text-white fw-bold mb-0">KasKita</h4>
                    </div>
                    <p class="small text-secondary mb-3">
                        Sistem pencatatan kas dan keuangan berbasis web yang dirancang khusus untuk mempermudah operasional pembukuan pelaku UMKM Indonesia.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-semibold mb-3">Navigasi</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#beranda">Beranda</a></li>
                        <li class="mb-2"><a href="#tentang">Tentang</a></li>
                        <li class="mb-2"><a href="#fitur">Fitur</a></li>
                        <li class="mb-2"><a href="#informasi">Informasi</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-semibold mb-3">Layanan</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('register') }}">Pencatatan Kas</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}">Kelola Kategori</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}">Laporan Keuangan</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}">Ekspor Excel & PDF</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-semibold mb-3">Kontak & Bantuan</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-success mt-1"></i>
                            <span>Sentra Bisnis UMKM, Jl. Koperasi Mandiri No. 45, Jakarta Selatan</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill text-success"></i>
                            <span>support@kaskita.id</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill text-success"></i>
                            <span>+62 812-3456-7890</span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
                <p class="mb-0">&copy; {{ date('Y') }} KasKita Indonesia. Hak Cipta Dilindungi.</p>
                <p class="mb-0">Dibuat dengan sepenuh hati untuk kemajuan UMKM Indonesia.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>