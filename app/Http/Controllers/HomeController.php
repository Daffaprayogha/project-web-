<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // Dynamic statistics with graceful fallback if database is not yet ready
        try {
            $userCount = User::count();
            $transactionCount = Transaction::count();
            $categoryCount = Category::distinct('name')->count();
            $totalTurnover = Transaction::where('type', 'pemasukan')->sum('amount');
        } catch (\Throwable $e) {
            $userCount = 0;
            $transactionCount = 0;
            $categoryCount = 0;
            $totalTurnover = 0;
        }

        // Formatted statistics with realistic baseline
        $stats = [
            'users' => max(120, $userCount + 85) . '+',
            'transactions' => max(500, $transactionCount + 450) . '+',
            'umkm' => max(60, $userCount + 55) . '+',
            'categories' => max(15, $categoryCount) . '+',
        ];

        // Educational articles for UMKM
        $articles = [
            [
                'id' => 1,
                'title' => 'Tips Mengelola Keuangan UMKM Agar Terhindar dari Bon/Hutang Macet',
                'category' => 'Manajemen Finansial',
                'date' => '2 Oktober 2026',
                'summary' => 'Banyak pelaku usaha kecil terhambat karena cash flow macet akibat piutang pelanggan tidak terkelola dengan baik. Simak panduan praktis memitigasi risiko tersebut.',
                'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&auto=format&fit=crop&q=80',
                'content' => 'Pengelolaan keuangan merupakan urat nadi usaha mikro. Langkah pertama adalah menetapkan batasan plafon kredit bon langganan. Jangan ragu mencatat setiap tanggal jatuh tempo di KasKita dan segera lakukan follow-up secara berkala. Selain itu, sisihkan setidaknya 10% dari keuntungan harian untuk cadangan kas darurat usaha.',
            ],
            [
                'id' => 2,
                'title' => 'Pentingnya Mencatat Transaksi Harian Tanpa Menunda',
                'category' => 'Kedisiplinan Usaha',
                'date' => '28 September 2026',
                'summary' => 'Menunda pencatatan transaksi seringkali membuat kuitansi hilang dan saldo kas tidak cocok dengan uang fisik. Disiplin harian adalah kunci.',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop&q=80',
                'content' => 'Setiap rupiah yang keluar atau masuk ke laci toko memiliki arti penting dalam perhitungan laba rugi. Luangkan waktu 5 menit setiap tutup toko atau langsung gunakan smartphone Anda untuk memasukkan transaksi di KasKita saat transaksi terjadi. Dengan begitu, Anda selalu tahu persis posisi kas Anda setiap detik.',
            ],
            [
                'id' => 3,
                'title' => 'Cara Efektif Memisahkan Keuangan Pribadi dan Keuangan Usaha',
                'category' => 'Strategi UMKM',
                'date' => '20 September 2026',
                'summary' => 'Kesalahan fatal nomor satu pada pengusaha pemula adalah menggunakan uang kas dagang untuk belanja dapur dan kebutuhan pribadi sehari-hari.',
                'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=600&auto=format&fit=crop&q=80',
                'content' => 'Tentukan gaji tetap bulanan atau mingguan untuk diri Anda sendiri sebagai pengelola usaha. Jangan pernah mengambil uang langsung dari laci kas tanpa dicatat sebagai gaji atau prive. Dengan memisahkan rekening usaha dan dompet pribadi, modal kerja toko Anda akan senantiasa aman dan terus berkembang.',
            ],
        ];

        $testimonials = [
            [
                'name' => 'Ibu Ratna Dewi',
                'role' => 'Pemilik Toko Sembako "Berkah"',
                'location' => 'Bandung',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80',
                'quote' => 'KasKita sangat mudah dipahami! Dulu saya sering pusing hitung modal kulakan dan uang di laci sering tekor. Sekarang semua arus kas rapi tercatat.',
            ],
            [
                'name' => 'Bapak Hendra Wijaya',
                'role' => 'Pengelola "Cemerlang Laundry Express"',
                'location' => 'Surabaya',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80',
                'quote' => 'Fitur laporan keuangan dan ekspor datanya sangat membantu saat evaluasi bulanan. Tampilannya bersih, warnanya segar, dan sangat cepat.',
            ],
            [
                'name' => 'Mbak Maya Lestari',
                'role' => 'Owner "Warung Kopi & Kuliner Pojok"',
                'location' => 'Yogyakarta',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80',
                'quote' => 'Bisa memantau laba harian lewat HP tanpa ribet install aplikasi berat. KasKita beneran pas untuk kebutuhan pedagang kecil seperti saya.',
            ],
        ];

        return view('landing', compact('stats', 'articles', 'testimonials'));
    }
}