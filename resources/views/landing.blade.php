@extends('layouts.landing')

@section('title', 'KasKita – Sistem Pencatatan Kas dan Keuangan UMKM Berbasis Web')

@section('content')

<!-- HERO SECTION -->
<section class="hero-section" id="beranda">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold mb-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-stars"></i> Solusi Finansial UMKM No. 1
                </span>
                <h1 class="hero-title mb-3">
                    Kelola Keuangan Usaha Jadi Lebih Mudah dengan <span class="text-success">KasKita</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    KasKita membantu pemilik UMKM mencatat pemasukan dan pengeluaran, memantau saldo, serta melihat laporan keuangan usaha secara sederhana dan terorganisir.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-kaskita btn-lg px-4 py-3">
                        <i class="bi bi-arrow-right-circle me-1"></i> Mulai Sekarang
                    </a>
                    <a href="#tentang" class="btn btn-kaskita-outline btn-lg px-4 py-3">
                        <i class="bi bi-info-circle me-1"></i> Pelajari KasKita
                    </a>
                </div>
                <div class="mt-4 pt-3 d-flex align-items-center gap-4 text-secondary small">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span>100% Aman</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-lightning-charge text-success fs-5"></i>
                        <span>Sangat Cepat</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-phone text-success fs-5"></i>
                        <span>Responsif Semua Layar</span>
                    </div>
                </div>
            </div>

            <!-- Graphic / Mockup Visual -->
            <div class="col-lg-6">
                <div class="position-relative">
                    <!-- Dashboard Preview Card -->
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden bg-white p-3 p-md-4" style="box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-success text-white p-2 rounded-3 d-inline-flex">
                                    <i class="bi bi-wallet2"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold">Toko Kelontong Berkah</h6>
                                    <span class="badge bg-success-subtle text-success small">UMKM Terverifikasi</span>
                                </div>
                            </div>
                            <span class="text-muted small"><i class="bi bi-clock"></i> Hari ini</span>
                        </div>

                        <!-- Mini Stats Row -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 border-start border-success border-4">
                                    <small class="text-secondary d-block">Pemasukan Hari Ini</small>
                                    <span class="fw-bold text-success fs-5">+Rp 2.450.000</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 border-start border-danger border-4">
                                    <small class="text-secondary d-block">Pengeluaran Operasional</small>
                                    <span class="fw-bold text-danger fs-5">-Rp 820.000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Saldo Box -->
                        <div class="p-3 rounded-3 mb-3 text-white" style="background: linear-gradient(135deg, #198754, #146c43);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50">Total Saldo Kas Usaha</small>
                                    <h4 class="fw-bold mb-0">Rp 14.850.000</h4>
                                </div>
                                <span class="badge bg-white text-success px-2 py-1"><i class="bi bi-graph-up-arrow"></i> Arus Kas Positif</span>
                            </div>
                        </div>

                        <!-- Sample Mini Transactions -->
                        <h6 class="fw-semibold small text-secondary mb-2">Pencatatan Terakhir</h6>
                        <div class="list-group list-group-flush small">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success rounded-circle p-2"><i class="bi bi-arrow-down-left"></i></span>
                                    <div>
                                        <div class="fw-semibold">Penjualan Beras & Minyak</div>
                                        <small class="text-muted">Kategori: Penjualan Produk</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-success">+Rp 650.000</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-circle p-2"><i class="bi bi-arrow-up-right"></i></span>
                                    <div>
                                        <div class="fw-semibold">Kulakan Stok Telur</div>
                                        <small class="text-muted">Kategori: Belanja Stok</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-danger">-Rp 380.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION TENTANG -->
<section class="py-5 bg-light" id="tentang">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="text-success fw-bold text-uppercase small tracking-wide">Tentang KasKita</span>
                <h2 class="section-title mt-2">Solusi Pembukuan Kas yang Praktis & Terpercaya</h2>
                <p class="text-secondary leading-relaxed mb-4">
                    KasKita merupakan sistem pencatatan kas berbasis web yang dirancang untuk membantu UMKM mengelola keuangan usaha dengan cara yang sederhana, cepat, dan mudah digunakan.
                </p>
                <div class="d-grid gap-3 mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Pencatatan Transaksi</h6>
                            <small class="text-secondary">Catat seluruh aliran dana tunai, transfer, dan bon pelanggan secara cepat dan teliti.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Pemantauan Saldo</h6>
                            <small class="text-secondary">Ketahui posisi uang kas secara real-time kapan saja tanpa perlu hitung manual berulang kali.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Pengelolaan Kategori</h6>
                            <small class="text-secondary">Kelompokkan pengeluaran dan pemasukan berdasarkan pos-pos operasional yang jelas.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Laporan Keuangan Sederhana</h6>
                            <small class="text-secondary">Rekapitulasi otomatis yang siap dilihat bulanan serta dicetak untuk keperluan evaluasi usaha.</small>
                        </div>
                    </div>
                </div>
                <a href="#fitur" class="btn btn-kaskita">
                    Selengkapnya <i class="bi bi-arrow-down-short"></i>
                </a>
            </div>

            <div class="col-lg-7">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                            <i class="bi bi-shop text-success fs-1 mb-3 d-block"></i>
                            <h5 class="fw-bold">Untuk Toko Kelontong</h5>
                            <p class="small text-secondary mb-0">Atur uang kulakan grosir, hasil penjualan harian, dan bon pelanggan tanpa takut selisih kas.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                            <i class="bi bi-basket3 text-success fs-1 mb-3 d-block"></i>
                            <h5 class="fw-bold">Untuk Usaha Laundry</h5>
                            <p class="small text-secondary mb-0">Catat pemasukan kiloan & satuan, pembelian deterjen, pewangi, dan biaya listrik operasional.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                            <i class="bi bi-cup-hot text-success fs-1 mb-3 d-block"></i>
                            <h5 class="fw-bold">Untuk Warung & Kuliner</h5>
                            <p class="small text-secondary mb-0">Hitung belanja bahan dapur segar pagi hari dan omzet kasir malam hari secara transparan.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                            <i class="bi bi-scissors text-success fs-1 mb-3 d-block"></i>
                            <h5 class="fw-bold">Untuk Jasa & Mikro</h5>
                            <p class="small text-secondary mb-0">Cocok untuk barbershop, bengkel motor, penjahit, dan aneka usaha mikro lainnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION FITUR / LAYANAN -->
<section class="py-5" id="fitur">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-success fw-bold text-uppercase small">Fitur Unggulan</span>
            <h2 class="section-title">Fitur Lengkap yang Dibutuhkan UMKM</h2>
            <p class="section-subtitle">Semua alat yang Anda butuhkan untuk mencatat, mengontrol, dan mengevaluasi kesehatan finansial usaha.</p>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper" style="background-color: #e8f5e9; color: #198754;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h5 class="fw-bold mb-2">1. Pencatatan Pemasukan</h5>
                    <p class="text-secondary small mb-0">
                        Mencatat seluruh uang yang masuk ke dalam usaha secara terperinci mulai dari penjualan tunai, online, hingga piutang yang telah terbayar.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper" style="background-color: #fee2e2; color: #dc3545;">
                        <i class="bi bi-cart-dash"></i>
                    </div>
                    <h5 class="fw-bold mb-2">2. Pencatatan Pengeluaran</h5>
                    <p class="text-secondary small mb-0">
                        Mencatat biaya operasional, kulakan stok barang dagangan, sewa, listrik, air, dan aneka pengeluaran usaha agar modal tidak tergerus.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper" style="background-color: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-tags"></i>
                    </div>
                    <h5 class="fw-bold mb-2">3. Manajemen Kategori</h5>
                    <p class="text-secondary small mb-0">
                        Mengelompokkan transaksi berdasarkan kategori kustom yang sesuai dengan karakteristik bisnis Anda agar pos pengeluaran terpantau rapi.
                    </p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper" style="background-color: #fef3c7; color: #d97706;">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <h5 class="fw-bold mb-2">4. Dashboard Keuangan</h5>
                    <p class="text-secondary small mb-0">
                        Melihat pemasukan, pengeluaran, dan saldo kas secara ringkas melalui tampilan visual kartu statistik dan grafik tren yang interaktif.
                    </p>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper" style="background-color: #f3e8ff; color: #7e22ce;">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </div>
                    <h5 class="fw-bold mb-2">5. Laporan Keuangan</h5>
                    <p class="text-secondary small mb-0">
                        Melihat laporan arus kas transaksi berdasarkan periode bulanan atau rentang tanggal tertentu untuk melihat laba kotor usaha.
                    </p>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper" style="background-color: #ccfbf1; color: #0f766e;">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                    </div>
                    <h5 class="fw-bold mb-2">6. Export Data (PDF & Excel)</h5>
                    <p class="text-secondary small mb-0">
                        Mengekspor laporan ke dalam format Excel (.csv) atau cetak langsung format PDF profesional yang siap diserahkan kepada mitra atau perbankan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION INFORMASI & EDUKASI -->
<section class="py-5 bg-light" id="informasi">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-success fw-bold text-uppercase small">Edukasi & Wawasan</span>
            <h2 class="section-title">Informasi & Tips Keuangan UMKM</h2>
            <p class="section-subtitle">Pelajari tips praktis mengelola cash flow dan meningkatkan efisiensi pembukuan usaha kecil.</p>
        </div>

        <div class="row g-4">
            @foreach($articles as $article)
            <div class="col-md-4">
                <div class="article-card">
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="article-img">
                    <div class="p-4 d-flex flex-column" style="min-height: 250px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success-subtle text-success small">{{ $article['category'] }}</span>
                            <small class="text-muted">{{ $article['date'] }}</small>
                        </div>
                        <h6 class="fw-bold mb-2 text-dark">{{ $article['title'] }}</h6>
                        <p class="small text-secondary mb-4 flex-grow-1">{{ $article['summary'] }}</p>
                        <button class="btn btn-outline-success btn-sm w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#articleModal{{ $article['id'] }}">
                            Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Article -->
            <div class="modal fade" id="articleModal{{ $article['id'] }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-4 shadow">
                        <div class="modal-header border-0 pb-0">
                            <span class="badge bg-success-subtle text-success">{{ $article['category'] }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 pt-2">
                            <h4 class="fw-bold mb-3">{{ $article['title'] }}</h4>
                            <p class="text-muted small mb-3"><i class="bi bi-calendar3"></i> {{ $article['date'] }} | Oleh Tim Edukasi KasKita</p>
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-100 rounded-3 mb-4" style="max-height: 300px; object-fit: cover;">
                            <div class="leading-relaxed text-secondary">
                                <p>{{ $article['content'] }}</p>
                                <p class="mb-0">Dengan KasKita, Anda tidak perlu khawatir transaksi tercecer. Seluruh catatan tersimpan rapi dan dapat diakses kapan saja melalui smartphone maupun laptop Anda.</p>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            <a href="{{ route('register') }}" class="btn btn-success btn-sm">Mulai Catat di KasKita</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SECTION STATISTIK -->
<section class="py-5">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['users'] }}</div>
                    <div class="text-secondary fw-semibold mt-1">Pengguna Terdaftar</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['transactions'] }}</div>
                    <div class="text-secondary fw-semibold mt-1">Transaksi Dicatat</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['umkm'] }}</div>
                    <div class="text-secondary fw-semibold mt-1">Mitra UMKM Aktif</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['categories'] }}</div>
                    <div class="text-secondary fw-semibold mt-1">Kategori Transaksi</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION TESTIMONI -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-success fw-bold text-uppercase small">Testimoni Pengguna</span>
            <h2 class="section-title">Dipercaya oleh Ratusan Pelaku Usaha</h2>
            <p class="section-subtitle">Dengarkan pengalaman para pemilik toko, pengelola laundry, dan pedagang yang telah terbantu oleh KasKita.</p>
        </div>

        <div class="row g-4">
            @foreach($testimonials as $t)
            <div class="col-md-4">
                <div class="testimonial-card shadow-sm">
                    <div class="text-warning mb-3">
                        @for($i = 0; $i < $t['rating']; $i++)
                            <i class="bi bi-star-fill"></i>
                        @endfor
                    </div>
                    <p class="fst-italic text-secondary mb-4">"{{ $t['quote'] }}"</p>
                    <div class="d-flex align-items-center gap-3 mt-auto">
                        <img src="{{ $t['avatar'] }}" alt="{{ $t['name'] }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $t['name'] }}</h6>
                            <small class="text-muted">{{ $t['role'] }} - {{ $t['location'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CALL TO ACTION (CTA) -->
<section class="py-5">
    <div class="container py-4">
        <div class="cta-box text-center shadow-lg">
            <div class="position-relative z-1 max-w-700 mx-auto">
                <h2 class="fw-bold mb-3 text-white display-6">Mulai Kelola Keuangan Usaha Anda Hari Ini</h2>
                <p class="text-white-50 fs-5 mb-4">
                    Catat transaksi, pantau saldo, dan kelola keuangan usaha dengan lebih mudah, cepat, dan rapi menggunakan KasKita.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 py-3 text-success fw-bold">
                        <i class="bi bi-person-plus-fill me-1"></i> Daftar Sekarang – Gratis!
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection