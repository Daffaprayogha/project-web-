<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\BillNotificationService;

class NovemberDataSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('username', 'pemilikumkm')->first();
        if (!$user) {
            $user = User::first();
        }

        if (!$user) {
            $this->command->error('User tidak ditemukan.');
            return;
        }

        $cats = Category::where('user_id', $user->id)->pluck('id', 'name');

        $transactions = [
            // Pemasukan November 2026
            [
                'category_id' => $cats['Penjualan Toko'] ?? null,
                'type' => 'pemasukan',
                'status' => 'lunas',
                'amount' => 3200000,
                'transaction_date' => '2026-11-01',
                'due_date' => null,
                'description' => 'Penjualan Sembako Awal Bulan',
                'notes' => 'Ramai pembeli awal bulan',
            ],
            [
                'category_id' => $cats['Penjualan Toko'] ?? null,
                'type' => 'pemasukan',
                'status' => 'lunas',
                'amount' => 4100000,
                'transaction_date' => '2026-11-04',
                'due_date' => null,
                'description' => 'Penjualan Beras & Minyak Goreng',
                'notes' => 'Pesanan langganan warung makan',
            ],
            [
                'category_id' => $cats['Piutang Pelanggan'] ?? null,
                'type' => 'pemasukan',
                'status' => 'lunas',
                'amount' => 850000,
                'transaction_date' => '2026-11-05',
                'due_date' => null,
                'description' => 'Pelunasan Bon Belanja Ibu Hj. Aminah',
                'notes' => 'Transfer via QRIS',
            ],
            [
                'category_id' => $cats['Penjualan Toko'] ?? null,
                'type' => 'pemasukan',
                'status' => 'lunas',
                'amount' => 3500000,
                'transaction_date' => '2026-11-06',
                'due_date' => null,
                'description' => 'Penjualan Harian Toko Kasir 1 & 2',
                'notes' => 'Rekap kasir sore',
            ],
            [
                'category_id' => $cats['Penjualan Toko'] ?? null,
                'type' => 'pemasukan',
                'status' => 'lunas',
                'amount' => 4800000,
                'transaction_date' => '2026-11-07',
                'due_date' => null,
                'description' => 'Penjualan Grosir Sembako & Paket Bahan Pokok',
                'notes' => 'Weekend ramai',
            ],

            // Pengeluaran November 2026 (Berbagai Kategori / Pos)
            [
                'category_id' => $cats['Gaji Karyawan'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 2800000,
                'transaction_date' => '2026-11-01',
                'due_date' => null,
                'description' => 'Gaji 2 Karyawan Toko Bulan November',
                'notes' => 'Ditransfer ke rekening masing-masing',
            ],
            [
                'category_id' => $cats['Kulakan / Stok Barang'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 5200000,
                'transaction_date' => '2026-11-02',
                'due_date' => null,
                'description' => 'Kulakan Beras 25 Karung & Minyak Goreng Curah',
                'notes' => 'Faktur No. STK-20261102',
            ],
            [
                'category_id' => $cats['Listrik, Air & Internet'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 480000,
                'transaction_date' => '2026-11-03',
                'due_date' => null,
                'description' => 'Pembayaran Tagihan Listrik PLN & WiFi Toko',
                'notes' => 'Token 500rb + tagihan internet',
            ],
            [
                'category_id' => $cats['Operasional & Kemasan'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 350000,
                'transaction_date' => '2026-11-04',
                'due_date' => null,
                'description' => 'Beli Plastik Kresek, Lakban, & Kantong Kertas',
                'notes' => 'Stok plastik untuk 1 bulan',
            ],
            [
                'category_id' => $cats['Kulakan / Stok Barang'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 2400000,
                'transaction_date' => '2026-11-05',
                'due_date' => null,
                'description' => 'Kulakan Telur Ayam, Gula Pasir & Mie Instan',
                'notes' => 'Distributor PT Sumber Pangan',
            ],
            [
                'category_id' => $cats['Sewa Tempat & Ruko'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 1500000,
                'transaction_date' => '2026-11-06',
                'due_date' => null,
                'description' => 'Iuran Sewa Kios Sembako Bulan November',
                'notes' => 'Kwitansi ada di laci kasir',
            ],
            [
                'category_id' => $cats['Pengeluaran Lainnya'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'lunas',
                'amount' => 250000,
                'transaction_date' => '2026-11-07',
                'due_date' => null,
                'description' => 'Biaya Kebersihan, Retribusi Sampah & Konsumsi Toko',
                'notes' => 'Iuran bulanan pasar/lingkungan',
            ],

            // Tagihan Belum Dibayar (Upcoming Due Dates)
            [
                'category_id' => $cats['Listrik, Air & Internet'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'belum_lunas',
                'amount' => 320000,
                'transaction_date' => '2026-11-07',
                'due_date' => '2026-11-10',
                'description' => 'Tagihan PDAM Air Bersih Toko Bulan November',
                'notes' => 'Jatuh tempo tanggal 10 November',
            ],
            [
                'category_id' => $cats['Kulakan / Stok Barang'] ?? null,
                'type' => 'pengeluaran',
                'status' => 'belum_lunas',
                'amount' => 1650000,
                'transaction_date' => '2026-11-06',
                'due_date' => '2026-11-12',
                'description' => 'Bon Kulakan Minuman Ringan & Kopi Kemasan',
                'notes' => 'Sales akan datang tagih tanggal 12 November',
            ],
        ];

        foreach ($transactions as $tx) {
            Transaction::create(array_merge($tx, ['user_id' => $user->id]));
        }

        BillNotificationService::syncForUser($user->id);

        $this->command->info(count($transactions) . ' transaksi November 2026 berhasil dibuat untuk user: ' . $user->name);
    }
}

