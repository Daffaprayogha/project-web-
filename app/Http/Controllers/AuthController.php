<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('login')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan masuk yang gagal. Silakan coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        $loginInput = $request->input('login');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Check if user exists & is active
        $user = User::where($fieldType, $loginInput)->first();
        if ($user && !$user->is_active) {
            return back()->withErrors(['login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.'])->withInput();
        }

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'Username/email atau password yang Anda masukkan salah.',
        ])->withInput();
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|unique:users,username|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda strip, dan underscore.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'business_name.required' => 'Nama usaha UMKM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type ?? 'Usaha Mikro',
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Seed default categories for this user
        $defaultCategories = [
            ['name' => 'Penjualan Produk', 'type' => 'pemasukan', 'icon' => 'bi-shop', 'color' => '#198754'],
            ['name' => 'Pendapatan Jasa', 'type' => 'pemasukan', 'icon' => 'bi-tools', 'color' => '#20c997'],
            ['name' => 'Piutang Terbayar', 'type' => 'pemasukan', 'icon' => 'bi-cash-coin', 'color' => '#0d6efd'],
            ['name' => 'Pemasukan Lainnya', 'type' => 'pemasukan', 'icon' => 'bi-wallet2', 'color' => '#146c43'],

            ['name' => 'Kulakan / Bahan Baku', 'type' => 'pengeluaran', 'icon' => 'bi-box-seam', 'color' => '#dc3545'],
            ['name' => 'Gaji Karyawan', 'type' => 'pengeluaran', 'icon' => 'bi-people', 'color' => '#fd7e14'],
            ['name' => 'Sewa Tempat & Ruko', 'type' => 'pengeluaran', 'icon' => 'bi-building', 'color' => '#6f42c1'],
            ['name' => 'Listrik, Air & Internet', 'type' => 'pengeluaran', 'icon' => 'bi-lightning-charge', 'color' => '#0dcaf0'],
            ['name' => 'Operasional Usaha', 'type' => 'pengeluaran', 'icon' => 'bi-bag', 'color' => '#d63384'],
            ['name' => 'Pengeluaran Lainnya', 'type' => 'pengeluaran', 'icon' => 'bi-credit-card', 'color' => '#6c757d'],
        ];

        foreach ($defaultCategories as $cat) {
            Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'type' => $cat['type'],
                'icon' => $cat['icon'],
                'color' => $cat['color'],
            ]);
        }

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Akun Berhasil Dibuat!',
            'message' => 'Selamat datang di KasKita! Mulai catat transaksi pemasukan dan pengeluaran usaha Anda dengan rapi.',
            'type' => 'success',
            'is_read' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di KasKita, ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }
}