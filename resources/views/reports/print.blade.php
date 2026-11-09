<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan KasKita – {{ $user->business_name }}</title>
    <!-- Favicon / Web Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 13px;
        }
        .report-header {
            border-bottom: 2px solid #198754;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .table-report th {
            background-color: #f1f5f9 !important;
            font-weight: 600;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body class="p-4">

    <!-- Action Bar (hidden on print) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border">
        <div>
            <strong>Pratinjau Cetak / Ekspor PDF</strong>
            <p class="text-muted small mb-0">Klik tombol di kanan atau tekan Ctrl+P untuk mencetak atau simpan sebagai PDF.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success fw-semibold">
                <i class="bi bi-printer"></i> Cetak Sekarang
            </button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>

    <!-- Official Report Header -->
    <div class="report-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-3 border" style="width: 55px; height: 55px; object-fit: cover;" alt="Logo">
            @endif
            <div>
                <h3 class="fw-bold text-success mb-1">KasKita</h3>
                <h5 class="fw-bold text-dark mb-0">{{ $user->business_name }}</h5>
                <small class="text-secondary">Jenis Usaha: {{ $user->business_type ?? 'UMKM' }} | Pemilik: {{ $user->name }} | No HP: {{ $user->phone ?? '-' }}</small>
            </div>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-secondary mb-1">LAPORAN KAS USAHA</h5>
            <span class="badge bg-success-subtle text-success fs-6">{{ $periodLabel }}</span>
            <small class="d-block text-muted mt-1">Dicetak pada: {{ date('d F Y, H:i') }} WIB</small>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="p-3 border rounded-3 bg-light">
                <small class="text-secondary d-block">Total Pemasukan</small>
                <h5 class="fw-bold text-success mb-0">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-4">
            <div class="p-3 border rounded-3 bg-light">
                <small class="text-secondary d-block">Total Pengeluaran</small>
                <h5 class="fw-bold text-danger mb-0">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-4">
            <div class="p-3 border rounded-3 bg-light">
                <small class="text-secondary d-block">Surplus / Defisit Kas</small>
                <h5 class="fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </h5>
            </div>
        </div>
    </div>

    <!-- Table of Transactions -->
    <table class="table table-bordered table-report align-middle mb-4">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 80px;">Jenis</th>
                <th style="width: 140px;">Kategori</th>
                <th>Keterangan & Catatan</th>
                <th class="text-end" style="width: 130px;">Pemasukan (Rp)</th>
                <th class="text-end" style="width: 130px;">Pengeluaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($transactions as $tx)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $tx->transaction_date->format('d/m/Y') }}</td>
                <td>{{ ucfirst($tx->type) }}</td>
                <td>{{ $tx->category->name ?? '-' }}</td>
                <td>
                    <strong>{{ $tx->description }}</strong>
                    @if($tx->notes)
                        <br><small class="text-muted">{{ $tx->notes }}</small>
                    @endif
                </td>
                <td class="text-end fw-semibold text-success">
                    {{ $tx->type === 'pemasukan' ? number_format($tx->amount, 0, ',', '.') : '-' }}
                </td>
                <td class="text-end fw-semibold text-danger">
                    {{ $tx->type === 'pengeluaran' ? number_format($tx->amount, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="5" class="text-end">JUMLAH TOTAL:</td>
                <td class="text-end text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="text-end text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Block -->
    <div class="row mt-5 pt-4">
        <div class="col-8">
            <p class="small text-muted mb-0">Catatan: Laporan ini dihasilkan secara otomatis oleh sistem KasKita.</p>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5 small text-secondary">Disetujui oleh Pemilik Usaha,</p>
            <div style="height: 40px;"></div>
            <strong class="d-block border-bottom pb-1 text-dark">{{ $user->name }}</strong>
            <small class="text-secondary">{{ $user->business_name }}</small>
        </div>
    </div>

</body>
</html>