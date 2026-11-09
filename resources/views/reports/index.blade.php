@extends('layouts.app')

@section('title', 'Laporan Keuangan UMKM')
@section('page_title', 'Laporan Keuangan UMKM')

@section('content')

<!-- FILTER PERIOD & ACTION BUTTONS -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Periode Laporan Keuangan</h5>
            <small class="text-secondary">Pilih laporan berdasarkan bulan kalender atau tentukan rentang tanggal kustom</small>
        </div>
        <div class="d-inline-flex gap-2">
            <a href="{{ route('reports.export-excel', request()->all()) }}" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Unduh Excel (.CSV)
            </a>
            <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="btn btn-kaskita btn-sm fw-semibold">
                <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
            </a>
        </div>
    </div>

    <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-secondary">Pilih Bulan</label>
            <select name="month" class="form-select form-select-sm" {{ request('start_date') ? 'disabled' : '' }} id="reportMonthSelect">
                @for($m = 1; $m <= 12; $m++)
                    @php $val = sprintf('%02d', $m); @endphp
                    <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Pilih Tahun</label>
            <select name="year" class="form-select form-select-sm" {{ request('start_date') ? 'disabled' : '' }} id="reportYearSelect">
                @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Rentang Dari</label>
            <input type="date" name="start_date" id="reportStartDate" class="form-control form-control-sm" value="{{ request('start_date') }}" placeholder="DD/MM/YYYY">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Sampai Tanggal</label>
            <input type="date" name="end_date" id="reportEndDate" class="form-control form-control-sm" value="{{ request('end_date') }}" placeholder="DD/MM/YYYY">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Jenis Kas</label>
            <select name="type" class="form-select form-select-sm">
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                <option value="pemasukan" {{ $type === 'pemasukan' ? 'selected' : '' }}>Pemasukan Saja</option>
                <option value="pengeluaran" {{ $type === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran Saja</option>
            </select>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-kaskita btn-sm w-100" title="Terapkan Filter Laporan">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->anyFilled(['start_date', 'end_date', 'month', 'year', 'type']))
                <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm border" title="Reset ke Bulan Ini">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
    @if(request('start_date') && request('end_date'))
        <div class="mt-2 text-primary small">
            <i class="bi bi-info-circle me-1"></i> Menampilkan laporan kustom tanggal <strong>{{ date('d M Y', strtotime(request('start_date'))) }}</strong> s/d <strong>{{ date('d M Y', strtotime(request('end_date'))) }}</strong>.
        </div>
    @endif
</div>

<!-- CASH FLOW SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-4 border-start border-success border-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Total Pemasukan ({{ $periodLabel }})</span>
                <span class="bg-success-subtle text-success p-2 rounded-3"><i class="bi bi-arrow-down-left fs-5"></i></span>
            </div>
            <h4 class="fw-bold text-success mb-0">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
            <small class="text-muted">Total penerimaan kas usaha</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-4 border-start border-danger border-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Total Pengeluaran ({{ $periodLabel }})</span>
                <span class="bg-danger-subtle text-danger p-2 rounded-3"><i class="bi bi-arrow-up-right fs-5"></i></span>
            </div>
            <h4 class="fw-bold text-danger mb-0">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
            <small class="text-muted">Beban operasional & modal kulakan</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-4 border-start border-primary border-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Surplus / Defisit Kas (Laba Bersih)</span>
                <span class="bg-primary-subtle text-primary p-2 rounded-3"><i class="bi bi-graph-up fs-5"></i></span>
            </div>
            <h4 class="fw-bold mb-0 {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                Rp {{ number_format($netBalance, 0, ',', '.') }}
            </h4>
            <small class="text-muted">{{ $netBalance >= 0 ? 'Kondisi kas usaha surplus sehat' : 'Pengeluaran melebihi pemasukan' }}</small>
        </div>
    </div>
</div>

<!-- CATEGORY BREAKDOWN TABLE -->
<div class="card-custom p-4 mb-4">
    <h5 class="fw-bold mb-1">Rincian Pos Kategori ({{ $periodLabel }})</h5>
    <small class="text-secondary d-block mb-3">Distribusi arus kas berdasarkan pos pengeluaran & pemasukan</small>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-secondary">
                <tr>
                    <th>NAMA KATEGORI</th>
                    <th>JENIS POS</th>
                    <th class="text-center">JUMLAH TRANSAKSI</th>
                    <th class="text-end">TOTAL NOMINAL</th>
                    <th class="text-end">KONTRIBUSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoryBreakdown as $cat)
                @php
                    $baseTotal = $cat->type === 'pemasukan' ? $totalIncome : $totalExpense;
                    $percent = $baseTotal > 0 ? round(($cat->total / $baseTotal) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="fw-semibold text-dark">{{ $cat->category_name }}</td>
                    <td>
                        @if($cat->type === 'pemasukan')
                            <span class="badge badge-income rounded-pill">Pemasukan</span>
                        @else
                            <span class="badge badge-expense rounded-pill">Pengeluaran</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $cat->count }}x</td>
                    <td class="text-end fw-bold {{ $cat->type === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($cat->total, 0, ',', '.') }}
                    </td>
                    <td class="text-end">
                        <span class="badge bg-light text-secondary border">{{ $percent }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi di periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- DETAILED TRANSACTIONS LIST -->
<div class="card-custom p-4">
    <h5 class="fw-bold mb-1">Kronologi Buku Kas Harian</h5>
    <small class="text-secondary d-block mb-3">Daftar transaksi kas berurutan sepanjang periode {{ $periodLabel }}</small>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light small text-secondary">
                <tr>
                    <th>NO</th>
                    <th>TANGGAL</th>
                    <th>JENIS</th>
                    <th>KATEGORI</th>
                    <th>KETERANGAN</th>
                    <th class="text-end">PEMASUKAN</th>
                    <th class="text-end">PENGELUARAN</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($transactions as $tx)
                <tr>
                    <td class="small text-muted">{{ $no++ }}</td>
                    <td class="small fw-semibold">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                    <td>
                        @if($tx->type === 'pemasukan')
                            <span class="badge badge-income rounded-pill small">Masuk</span>
                        @else
                            <span class="badge badge-expense rounded-pill small">Keluar</span>
                        @endif
                    </td>
                    <td class="small">{{ $tx->category->name ?? 'Tanpa Kategori' }}</td>
                    <td>
                        <strong class="d-block small text-dark">{{ $tx->description }}</strong>
                        @if($tx->notes)
                            <small class="text-muted">{{ $tx->notes }}</small>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-success">
                        {{ $tx->type === 'pemasukan' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end fw-bold text-danger">
                        {{ $tx->type === 'pengeluaran' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi tercatat.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end">TOTAL KESELURUHAN:</td>
                    <td class="text-end text-success">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection