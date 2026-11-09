@extends('layouts.app')

@section('title', 'Profil & Usaha UMKM')
@section('page_title', 'Profil & Usaha UMKM')

@section('content')

<div class="row g-4">
    <!-- Profil & Identitas Usaha -->
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1">Identitas Pemilik & Usaha</h5>
            <small class="text-secondary d-block mb-4">Perbarui informasi profil pribadi dan nama usaha Anda</small>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Avatar / Logo Toko Upload -->
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4 border">
                    <div>
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle border shadow-sm" style="width: 72px; height: 72px; object-fit: cover;" alt="Logo Toko">
                        @else
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3 shadow-sm" style="width: 72px; height: 72px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <label class="form-label small fw-semibold text-secondary mb-1">Foto Profil / Logo Toko</label>
                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Maksimal 2MB. Format JPG, PNG, WEBP.</small>
                        @if($user->avatar)
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_avatar" id="removeAvatarCheck" value="1">
                                <label class="form-check-label text-danger small" for="removeAvatarCheck">Hapus foto profil saat ini</label>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Nama Lengkap Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Nama Usaha / Toko <span class="text-danger">*</span></label>
                        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name', $user->business_name) }}" required>
                        @error('business_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary">Jenis Usaha</label>
                        <input type="text" name="business_type" class="form-control @error('business_type') is-invalid @enderror" value="{{ old('business_type', $user->business_type) }}">
                        @error('business_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-kaskita mt-4 px-4">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                </button>
            </form>
        </div>
    </div>

    <!-- Ubah Password -->
    <div class="col-lg-5">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1">Keamanan Akun</h5>
            <small class="text-secondary d-block mb-4">Ganti password akun KasKita secara berkala</small>

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Password Saat Ini <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password lama" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Password Baru (Min 8 karakter) <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password baru" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi password baru" required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-outline-danger w-100 fw-semibold">
                    <i class="bi bi-shield-lock me-1"></i> Perbarui Password
                </button>
            </form>
        </div>
    </div>
</div>

@endsection