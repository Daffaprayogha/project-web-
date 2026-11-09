@extends('layouts.app')

@section('title', 'Pengaturan & Cadangan Data')
@section('page_title', 'Pengaturan Aplikasi')

@section('content')

<!-- RINGKASAN DATA KAS USAHA -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 bg-white border-start border-success border-4 h-100">
            <small class="text-secondary d-block">Total Transaksi Kas</small>
            <h4 class="fw-bold text-dark mb-0">{{ number_format($totalTransactions, 0, ',', '.') }} Catatan</h4>
            <small class="text-muted">Sejak bergabung</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 bg-white border-start border-info border-4 h-100">
            <small class="text-secondary d-block">Pos Kategori Aktif</small>
            <h4 class="fw-bold text-dark mb-0">{{ $totalCategories }} Pos</h4>
            <small class="text-muted">Pemasukan & Pengeluaran</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 bg-white border-start border-success border-4 h-100">
            <small class="text-secondary d-block">Akumulasi Pemasukan</small>
            <h4 class="fw-bold text-success mb-0">+Rp {{ number_format($totalIncomeAllTime, 0, ',', '.') }}</h4>
            <small class="text-muted">Seluruh arus masuk</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 bg-white border-start border-danger border-4 h-100">
            <small class="text-secondary d-block">Akumulasi Pengeluaran</small>
            <h4 class="fw-bold text-danger mb-0">-Rp {{ number_format($totalExpenseAllTime, 0, ',', '.') }}</h4>
            <small class="text-muted">Seluruh arus keluar</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- BACKUP & MANAJEMEN DATA -->
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Cadangan & Ekspor Data Kas</h5>
                    <small class="text-secondary">Amankan seluruh pembukuan keuangan usaha Anda dalam format terbuka</small>
                </div>
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i> Data Aman</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="bg-primary text-white p-2 rounded-2"><i class="bi bi-file-earmark-code"></i></span>
                                <strong class="text-dark">Cadangan JSON (Full Backup)</strong>
                            </div>
                            <p class="small text-secondary mb-3">
                                Unduh arsip lengkap mencakup data usaha, seluruh pos kategori, dan riwayat mutasi transaksi kas dalam format JSON.
                            </p>
                        </div>
                        <a href="{{ route('settings.backup-json') }}" class="btn btn-primary btn-sm fw-semibold w-100">
                            <i class="bi bi-download me-1"></i> Unduh Cadangan JSON
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="bg-success text-white p-2 rounded-2"><i class="bi bi-file-earmark-spreadsheet"></i></span>
                                <strong class="text-dark">Laporan Spreadsheet (CSV)</strong>
                            </div>
                            <p class="small text-secondary mb-3">
                                Buka dan olah data keuangan menggunakan Microsoft Excel, Google Sheets, atau aplikasi spreadsheet lainnya.
                            </p>
                        </div>
                        <a href="{{ route('reports.export-excel') }}" class="btn btn-outline-success btn-sm fw-semibold w-100">
                            <i class="bi bi-file-spreadsheet me-1"></i> Ekspor ke Excel (.CSV)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PREFERENSI SISTEM -->
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold mb-1">Preferensi Sistem KasKita</h5>
            <small class="text-secondary d-block mb-4">Konfigurasi format mata uang, zona waktu, dan tampilan usaha</small>

            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                    <div>
                        <strong class="text-dark d-block">Mata Uang Default</strong>
                        <small class="text-secondary">Format mata uang untuk seluruh transaksi kas</small>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">IDR (Rupiah Indonesia - Rp)</span>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                    <div>
                        <strong class="text-dark d-block">Zona Waktu</strong>
                        <small class="text-secondary">Waktu pencatatan buku kas harian</small>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">Asia/Jakarta (WIB)</span>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                    <div>
                        <strong class="text-dark d-block">Format Tanggal Laporan</strong>
                        <small class="text-secondary">Tampilan tanggal pada tabel dan ekspor PDF</small>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">DD/MM/YYYY</span>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                    <div>
                        <strong class="text-dark d-block">Tema Warna Tampilan</strong>
                        <small class="text-secondary">Palet warna khas finansial UMKM</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-circle" style="width: 20px; height: 20px; background-color: #198754;"></span>
                        <span class="small fw-semibold text-success">Hijau KasKita (#198754)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1">Tentang Sistem KasKita</h5>
            <small class="text-secondary d-block mb-3">Informasi versi dan lisensi platform</small>
            <p class="small text-secondary mb-2">
                KasKita – Sistem Pencatatan Kas dan Keuangan UMKM Berbasis Web dikembangkan untuk menjawab kebutuhan pembukuan digital yang mudah, terjangkau, dan langsung siap pakai bagi pemilik usaha mikro, toko kelontong, jasa laundry, dan pedagang di seluruh Indonesia.
            </p>
            <div class="p-3 bg-light rounded-3 border small text-muted">
                <strong>Versi:</strong> KasKita v1.0.0 (Release Edition)<br>
                <strong>Framework:</strong> Laravel Framework 9.x / PHP 8.x<br>
                <strong>Basis Data:</strong> MySQL Relational Database
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4 bg-light border-0 mb-4">
            <h6 class="fw-bold text-success mb-2"><i class="bi bi-lightbulb-fill me-1"></i> Panduan Singkat UMKM</h6>
            <p class="small text-secondary mb-3">Ikuti 3 langkah mudah menjaga kas usaha tetap sehat:</p>
            <ol class="small text-secondary ps-3 mb-0">
                <li class="mb-2"><strong>Catat Setiap Hari:</strong> Masukkan uang masuk dan keluar sebelum menutup toko.</li>
                <li class="mb-2"><strong>Pakai Kategori:</strong> Pisahkan pos belanja modal dengan biaya operasional.</li>
                <li class="mb-0"><strong>Cetak Laporan Bulanan:</strong> Evaluasi apakah usaha Anda bulan ini menghasilkan laba bersih positif.</li>
            </ol>
        </div>

        <div class="card-custom p-4 border">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-headset me-1 text-primary"></i> Bantuan & Kontak</h6>
            <p class="small text-secondary mb-3">Butuh bantuan atau ingin menyampaikan masukan seputar sistem KasKita?</p>
            <div class="small text-secondary">
                <div class="mb-2"><i class="bi bi-envelope me-2 text-success"></i> support@kaskita.id</div>
                <div class="mb-2"><i class="bi bi-whatsapp me-2 text-success"></i> +62 812-3456-7890</div>
                <div><i class="bi bi-clock me-2 text-success"></i> Senin - Sabtu, 08:00 - 17:00 WIB</div>
            </div>
        </div>
    </div>
</div>

@endsection