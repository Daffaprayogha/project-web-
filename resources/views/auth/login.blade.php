@extends('layouts.auth')

@section('title', 'Masuk ke KasKita – Sistem Pencatatan Kas UMKM')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <!-- Brand Header with animation -->
        <div class="text-center mb-4 animate-brand">
            <a href="{{ route('home') }}" class="brand-link">
                <img src="{{ asset('img/logo.svg') }}" alt="KasKita Logo" style="width: 46px; height: 46px; object-fit: contain;" class="rounded-3 shadow-sm logo-icon-pulse">
                <span>KasKita</span>
            </a>
            <p class="text-secondary small mt-2 mb-0">Sistem Pencatatan Kas dan Keuangan UMKM</p>
        </div>

        <!-- Auth Card with entrance animation -->
        <div class="auth-card p-4 p-md-5 animate-card" id="loginCard">
            <!-- Loading progress indicator on top of card -->
            <div class="card-progress-bar" id="cardProgress"></div>

            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="fw-bold text-dark mb-0">Masuk ke KasKita</h4>
                    <span class="badge bg-success-subtle text-success small rounded-pill px-2">v1.0</span>
                </div>
                <p class="text-secondary small">Kelola kas dan pantau keuangan usaha Anda dengan rapi.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shake-alert" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-circle-fill text-danger fs-5 mt-1"></i>
                        <ul class="mb-0 small ps-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm" onsubmit="handleLoginSubmit(event)">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="login" class="form-control border-start-0 @error('login') is-invalid @enderror" 
                               placeholder="Masukkan username atau email" value="{{ old('login') }}" required autofocus id="loginInput">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold small text-secondary mb-0">Password</label>
                        <a href="javascript:void(0)" onclick="alert('Untuk demonstrasi, Anda dapat masuk menggunakan akun demo atau membuat akun baru melalui halaman Register.')" class="text-success small text-decoration-none">Lupa Password?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                               placeholder="Masukkan password" required id="passwordInput">
                    </div>
                </div>

                <div class="mb-4 form-check d-flex justify-content-between align-items-center">
                    <div>
                        <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-secondary ms-1" for="rememberCheck">Ingat saya</label>
                    </div>
                </div>

                <!-- Animated Login Button -->
                <button type="submit" class="btn btn-kaskita w-100 mb-3" id="btnLogin">
                    <span id="btnText">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </span>
                    <span id="btnSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Memverifikasi...
                    </span>
                </button>
            </form>

            <!-- Quick Demo Button -->
            <div class="p-3 bg-light rounded-4 border mb-4 position-relative overflow-hidden" style="transition: all 0.25s ease;" onmouseover="this.style.borderColor='#198754'" onmouseout="this.style.borderColor='#dee2e6'">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-success-subtle text-success fw-semibold"><i class="bi bi-lightning-charge-fill me-1"></i>Akun Demo Siap Pakai</span>
                    <button type="button" class="btn btn-sm btn-link text-success p-0 fw-semibold text-decoration-none" onclick="fillDemo()">
                        <i class="bi bi-magic me-1"></i> Isi Otomatis
                    </button>
                </div>
                <div class="small text-secondary mt-1">
                    User: <strong class="text-dark">pemilikumkm</strong> | Pass: <strong class="text-dark">password123</strong>
                </div>
            </div>

            <div class="text-center small text-secondary">
                Belum punya akun? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Daftar Akun Baru</a>
            </div>
        </div>

        <div class="text-center mt-4 animate-footer">
            <a href="{{ route('home') }}" class="text-secondary small text-decoration-none d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
function fillDemo() {
    const loginInput = document.getElementById('loginInput');
    const passwordInput = document.getElementById('passwordInput');
    
    // Fill with smooth highlight animation
    loginInput.value = 'pemilikumkm';
    passwordInput.value = 'password123';

    loginInput.style.backgroundColor = '#e8f5e9';
    passwordInput.style.backgroundColor = '#e8f5e9';

    setTimeout(() => {
        loginInput.style.backgroundColor = '';
        passwordInput.style.backgroundColor = '';
    }, 600);
}

function handleLoginSubmit(event) {
    const btn = document.getElementById('btnLogin');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const cardProgress = document.getElementById('cardProgress');
    const overlay = document.getElementById('authOverlay');

    // Show progress bar on top of card
    cardProgress.style.display = 'block';

    // Disable button & switch to spinner
    btn.disabled = true;
    btnText.classList.add('d-none');
    btnSpinner.classList.remove('d-none');

    // Show smooth overlay
    overlay.classList.add('active');
}
</script>
@endsection