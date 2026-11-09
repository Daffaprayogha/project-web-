<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Default Demo User
        $user = User::create([
            'name' => 'Budi Pratama',
            'username' => 'pemilikumkm',
            'email' => 'pemilik@kaskita.id',
            'phone' => '081234567890',
            'business_name' => 'Toko Sembako Berkah Jaya',
            'business_type' => 'Toko Kelontong & Sembako',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // 2. Default Categories
        $incomeCategories = [
            ['name' => 'Penjualan Toko', 'icon' => 'bi-shop', 'color' => '#198754'],
            ['name' => 'Pendapatan Jasa', 'icon' => 'bi-tools', 'color' => '#20c997'],
            ['name' => 'Piutang Pelanggan', 'icon' => 'bi-cash-coin', 'color' => '#0d6efd'],
            ['name' => 'Pendapatan Lain-lain', 'icon' => 'bi-wallet2', 'color' => '#146c43'],
        ];

        $expenseCategories = [
            ['name' => 'Kulakan / Stok Barang', 'icon' => 'bi-box-seam', 'color' => '#dc3545'],
            ['name' => 'Gaji Karyawan', 'icon' => 'bi-people', 'color' => '#fd7e14'],
            ['name' => 'Sewa Tempat & Ruko', 'icon' => 'bi-building', 'color' => '#6f42c1'],
            ['name' => 'Listrik, Air & Internet', 'icon' => 'bi-lightning-charge', 'color' => '#0dcaf0'],
            ['name' => 'Operasional & Kemasan', 'icon' => 'bi-bag', 'color' => '#d63384'],
            ['name' => 'Pengeluaran Lainnya', 'icon' => 'bi-credit-card', 'color' => '#6c757d'],
        ];

        $catMap = [];

        foreach ($incomeCategories as $c) {
            $cat = Category::create([
                'user_id' => $user->id,
                'name' => $c['name'],
                'type' => 'pemasukan',
                'icon' => $c['icon'],
                'color' => $c['color'],
            ]);
            $catMap[$c['name']] = $cat->id;
        }

        foreach ($expenseCategories as $c) {
            $cat = Category::create([
                'user_id' => $user->id,
                'name' => $c['name'],
                'type' => 'pengeluaran',
                'icon' => $c['icon'],
                'color' => $c['color'],
            ]);
            $catMap[$c['name']] = $cat->id;
        }

        // 3. Transactions for current month and previous month
        $now = Carbon::now();
        $sampleTransactions = [
            // Pemasukan bulan ini
            ['type' => 'pemasukan', 'cat' => 'Penjualan Toko', 'amount' => 1750000, 'date' => $now->copy()->subDays(1), 'desc' => 'Hasil penjualan sembako dan minyak goreng hari ini'],
            ['type' => 'pemasukan', 'cat' => 'Penjualan Toko', 'amount' => 2200000, 'date' => $now->copy()->subDays(2), 'desc' => 'Penjualan beras dan sembako grosir kecil'],
            ['type' => 'pemasukan', 'cat' => 'Piutang Pelanggan', 'amount' => 500000, 'date' => $now->copy()->subDays(3), 'desc' => 'Pelunasan bon langganan Ibu Siti'],
            ['type' => 'pemasukan', 'cat' => 'Penjualan Toko', 'amount' => 1950000, 'date' => $now->copy()->subDays(4), 'desc' => 'Penjualan harian toko'],
            ['type' => 'pemasukan', 'cat' => 'Pendapatan Jasa', 'amount' => 350000, 'date' => $now->copy()->subDays(5), 'desc' => 'Jasa pesan antar sembako catering'],

            // Pengeluaran bulan ini
            ['type' => 'pengeluaran', 'cat' => 'Kulakan / Stok Barang', 'amount' => 2800000, 'date' => $now->copy()->subDays(2), 'desc' => 'Belanja kulakan beras 10 karung & minyak goreng'],
            ['type' => 'pengeluaran', 'cat' => 'Listrik, Air & Internet', 'amount' => 320000, 'date' => $now->copy()->subDays(4), 'desc' => 'Pembayaran token listrik toko bulan ini'],
            ['type' => 'pengeluaran', 'cat' => 'Operasional & Kemasan', 'amount' => 150000, 'date' => $now->copy()->subDays(5), 'desc' => 'Beli plastik kresek & tali rafia'],

            // Transaksi 2 minggu lalu
            ['type' => 'pemasukan', 'cat' => 'Penjualan Toko', 'amount' => 3100000, 'date' => $now->copy()->subDays(10), 'desc' => 'Penjualan ramai weekend'],
            ['type' => 'pengeluaran', 'cat' => 'Kulakan / Stok Barang', 'amount' => 1900000, 'date' => $now->copy()->subDays(11), 'desc' => 'Kulakan mie instan, telur, dan gula pasir'],
            ['type' => 'pengeluaran', 'cat' => 'Gaji Karyawan', 'amount' => 1500000, 'date' => $now->copy()->subDays(15), 'desc' => 'Gaji paruh waktu penjaga toko'],

            // Transaksi bulan lalu (untuk grafik tren)
            ['type' => 'pemasukan', 'cat' => 'Penjualan Toko', 'amount' => 14500000, 'date' => $now->copy()->subMonth()->startOfMonth()->addDays(5), 'desc' => 'Total rekap penjualan minggu 1 bulan lalu'],
            ['type' => 'pengeluaran', 'cat' => 'Kulakan / Stok Barang', 'amount' => 8200000, 'date' => $now->copy()->subMonth()->startOfMonth()->addDays(6), 'desc' => 'Kulakan besar awal bulan lalu'],
            ['type' => 'pengeluaran', 'cat' => 'Sewa Tempat & Ruko', 'amount' => 2000000, 'date' => $now->copy()->subMonth()->startOfMonth()->addDays(1), 'desc' => 'Sewa ruko toko bulan lalu'],
        ];

        foreach ($sampleTransactions as $tx) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $catMap[$tx['cat']] ?? null,
                'type' => $tx['type'],
                'status' => 'lunas',
                'amount' => $tx['amount'],
                'transaction_date' => $tx['date']->format('Y-m-d'),
                'description' => $tx['desc'],
                'notes' => 'Tercatat otomatis di sistem KasKita',
            ]);
        }

        // 4. Sample Unpaid Bills with Due Dates
        $unpaidSample = [
            [
                'cat' => 'Listrik, Air & Internet',
                'amount' => 450000,
                'due_date' => $now->copy()->addDays(2),
                'desc' => 'Tagihan Listrik & WiFi Toko Bulan Ini',
                'notes' => 'Batas tanggal bayar 2 hari lagi',
            ],
            [
                'cat' => 'Sewa Tempat & Ruko',
                'amount' => 2000000,
                'due_date' => $now->copy()->addDays(5),
                'desc' => 'Sewa Kios Tambahan',
                'notes' => 'Jatuh tempo sewa tempat',
            ],
            [
                'cat' => 'Kulakan / Stok Barang',
                'amount' => 1250000,
                'due_date' => $now->copy()->subDays(1),
                'desc' => 'Nota Kulakan Telur Supplier Jaya (Tempo)',
                'notes' => 'Sudah lewat tanggal jatuh tempo 1 hari',
            ],
        ];

        foreach ($unpaidSample as $b) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $catMap[$b['cat']] ?? null,
                'type' => 'pengeluaran',
                'status' => 'belum_lunas',
                'amount' => $b['amount'],
                'transaction_date' => $now->format('Y-m-d'),
                'due_date' => $b['due_date']->format('Y-m-d'),
                'description' => $b['desc'],
                'notes' => $b['notes'],
            ]);
        }

        // Sync notifications for the unpaid bills
        \App\Services\BillNotificationService::syncForUser($user->id);

        // 5. Welcome Notification
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Selamat Datang di KasKita!',
            'message' => 'Akun Toko Sembako Berkah Jaya telah berhasil dibuat. Mulai kelola kas usaha Anda sekarang.',
            'type' => 'success',
            'is_read' => true,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Tips Keuangan UMKM',
            'message' => 'Pisahkan selalu uang pribadi dan kas usaha untuk menjaga stabilitas perputaran modal.',
            'type' => 'info',
            'is_read' => false,
        ]);

        echo "Seed data created successfully!\n";
    }
}