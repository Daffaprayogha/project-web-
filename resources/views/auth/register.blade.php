@extends('layouts.auth')

@section('title', 'Daftar Akun KasKita – Sistem Pencatatan Kas UMKM')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="brand-link">
                <img src="{{ asset('img/logo.svg') }}" alt="KasKita Logo" style="width: 44px; height: 44px; object-fit: contain;" class="rounded-3 shadow-sm">
                <span>KasKita</span>
            </a>
            <p class="text-secondary small mt-2">Daftar Akun Usaha UMKM Anda</p>
        </div>

        <div class="auth-card p-4 p-md-5">
            <div class="mb-4">
                <h4 class="fw-bold text-dark mb-1">Daftar Akun KasKita</h4>
                <p class="text-secondary small">Mulai kelola keuangan usaha Anda dengan rapi dan terstruktur.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Nama Lengkap Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Contoh: Budi Pratama" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                               placeholder="Contoh: budipratama" value="{{ old('username') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="budi@example.com" value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               placeholder="Contoh: 081234567890" value="{{ old('phone') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Nama Usaha / Toko <span class="text-danger">*</span></label>
                        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                               placeholder="Contoh: Toko Sembako Berkah" value="{{ old('business_name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Jenis Usaha</label>
                        <select name="business_type" class="form-control">
                            <option value="Toko Kelontong / Sembako">Toko Kelontong / Sembako</option>
                            <option value="Laundry Kiloan / Satuan">Laundry Kiloan / Satuan</option>
                            <option value="Warung Makan / Kuliner">Warung Makan / Kuliner</option>
                            <option value="Pedagang Pasar / Kios">Pedagang Pasar / Kios</option>
                            <option value="Jasa Servis / Bengkel">Jasa Servis / Bengkel</option>
                            <option value="Usaha Mikro Lainnya">Usaha Mikro Lainnya</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Password (Min. 8 karakter) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimal 8 karakter" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" 
                               placeholder="Ulangi password" required>
                    </div>
                </div>

                <div class="form-check my-3">
                    <input class="form-check-input" type="checkbox" required id="termsCheck">
                    <label class="form-check-label small text-secondary" for="termsCheck">
                        Saya menyetujui ketentuan penggunaan dan privasi sistem KasKita.
                    </label>
                </div>

                <button type="submit" class="btn btn-kaskita w-100 py-3 mb-3">
                    <i class="bi bi-person-check me-1"></i> Daftar Sekarang
                </button>
            </form>

            <div class="text-center small text-secondary">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Masuk di sini</a>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-secondary small text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection