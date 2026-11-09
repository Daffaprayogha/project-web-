@extends('layouts.app')

@section('title', 'Dashboard Keuangan')
@section('page_title', 'Dashboard Keuangan')

@section('styles')
<style>
    /* Keyframe Animations for Dashboard Entrance (Runs Once on Enter / Refresh) */
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUpStagger {
        from { opacity: 0; transform: translateY(22px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes tableRowEntrance {
        from { opacity: 0; transform: translateX(-12px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .anim-greeting {
        animation: slideInDown 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-stat-1 { animation: fadeInUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) both 0.08s; }
    .anim-stat-2 { animation: fadeInUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) both 0.16s; }
    .anim-stat-3 { animation: fadeInUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) both 0.24s; }
    .anim-stat-4 { animation: fadeInUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) both 0.32s; }

    .anim-chart-left { animation: fadeInUpStagger 0.7s cubic-bezier(0.16, 1, 0.3, 1) both 0.38s; }
    .anim-chart-right { animation: fadeInUpStagger 0.7s cubic-bezier(0.16, 1, 0.3, 1) both 0.44s; }
    .anim-table { animation: fadeInUpStagger 0.7s cubic-bezier(0.16, 1, 0.3, 1) both 0.50s; }

    /* Live Scrubbing Inspector Banner */
    .touch-inspector-card {
        background: linear-gradient(135deg, #f8fafc 0%, #edf7f2 100%);
        border: 1px solid #d1e7dd;
        border-radius: 12px;
        padding: 10px 16px;
        transition: all 0.3s ease;
    }

    /* Mode Switcher Buttons (Per Hari, Per Bulan, Per Tahun) */
    .period-switch-btn {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        border: 1px solid transparent;
        background: transparent;
        color: #64748b;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .period-switch-btn:hover {
        color: #198754;
    }
    .period-switch-btn.active {
        background: #198754;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.25);
    }

    /* Interactive Donut Category Badges */
    .cat-interactive-pill {
        cursor: pointer;
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }
    .cat-interactive-pill:hover, .cat-interactive-pill.active {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        border-color: #dc3545;
        background: #fee2e2;
        color: #842029;
        font-weight: 600;
    }

    /* Table row hover highlight */
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f8f4 !important;
    }
</style>
@endsection

@section('content')

<!-- 1. GREETING BANNER WITH ENTRANCE ANIMATION -->
<div class="card-custom p-4 mb-4 text-white position-relative overflow-hidden anim-greeting" style="background: linear-gradient(135deg, #198754 0%, #0f5132 100%); box-shadow: 0 10px 25px rgba(25, 135, 84, 0.2);">
    <div class="row align-items-center">
        <div class="col-md-8">
            <span class="badge bg-white-50 text-white rounded-pill px-3 py-1 mb-2 small d-inline-flex align-items-center gap-1">
                <i class="bi bi-shield-check"></i> Status Kas Aktif
            </span>
            <h3 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h3>
            <p class="mb-0 text-white-50">
                Berikut adalah ringkasan performa finansial usaha <strong>{{ Auth::user()->business_name }}</strong> untuk periode {{ date('F Y') }}.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2">
                <button class="btn btn-light btn-sm fw-semibold text-success shadow-sm" onclick="openQuickTx('pemasukan')">
                    <i class="bi bi-arrow-down-left"></i> + Pemasukan
                </button>
                <button class="btn btn-outline-light btn-sm fw-semibold shadow-sm" onclick="openQuickTx('pengeluaran')">
                    <i class="bi bi-arrow-up-right"></i> - Pengeluaran
                </button>
            </div>
        </div>
    </div>
</div>

@if(isset($unpaidBills) && $unpaidBills->isNotEmpty())
@php
    $unpaidTotal = $unpaidBills->sum('amount');
    $overdueItems = $unpaidBills->filter(fn($b) => $b->due_date && \Carbon\Carbon::parse($b->due_date)->startOfDay()->lt(\Carbon\Carbon::today()));
@endphp
<div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <span class="p-2 rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-bell-fill fs-5"></i>
        </span>
        <div>
            <strong class="text-dark d-block">
                Ada {{ $unpaidBills->count() }} Pengeluaran / Tagihan Belum Dibayar (Total: Rp {{ number_format($unpaidTotal, 0, ',', '.') }})
            </strong>
            <small class="text-secondary">
                @if($overdueItems->count() > 0)
                    <span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $overdueItems->count() }} tagihan sudah lewat batas tanggal bayar!</span>
                @else
                    Periksa jadwal tanggal bayar di menu Notifikasi untuk menjaga kelancaran operasional usaha Anda.
                @endif
            </small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('notifications.index') }}" class="btn btn-warning btn-sm fw-semibold text-dark shadow-sm">
            <i class="bi bi-calendar-check me-1"></i> Buka Notifikasi & Bayar
        </a>
    </div>
</div>
@endif

<!-- 2. 4 STATISTIC CARDS WITH STAGGERED ENTRANCE & LIVE NUMBER COUNTER -->
<div class="row g-3 mb-4">
    <!-- Total Saldo Kas -->
    <div class="col-sm-6 col-xl-3 anim-stat-1">
        <div class="card-custom p-3 h-100 position-relative overflow-hidden" style="border-left: 5px solid #198754;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Total Saldo Kas Usaha</span>
                <span class="bg-success-subtle text-success p-2 rounded-3">
                    <i class="bi bi-wallet2 fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 {{ $currentBalance >= 0 ? 'text-success' : 'text-danger' }}" id="counterBalance" data-target="{{ $currentBalance }}">
                Rp 0
            </h4>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="text-muted">Kas Kumulatif</small>
                <span class="badge {{ $currentMonthNet >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill" style="font-size: 11px;">
                    Bulan ini: {{ $currentMonthNet >= 0 ? '+' : '' }}Rp {{ number_format($currentMonthNet, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Pemasukan Bulan Ini -->
    <div class="col-sm-6 col-xl-3 anim-stat-2">
        <div class="card-custom p-3 h-100 position-relative overflow-hidden" style="border-left: 5px solid #20c997;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Pemasukan Bulan Ini</span>
                <span class="bg-success-subtle text-success p-2 rounded-3">
                    <i class="bi bi-arrow-down-left fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-success" id="counterIncome" data-target="{{ $currentMonthIncome }}">
                Rp 0
            </h4>
            <div class="small">
                <span class="text-success fw-semibold"><i class="bi bi-arrow-up"></i> {{ $incomeGrowth }}%</span>
                <span class="text-muted">vs bulan lalu</span>
            </div>
        </div>
    </div>

    <!-- Pengeluaran Bulan Ini -->
    <div class="col-sm-6 col-xl-3 anim-stat-3">
        <div class="card-custom p-3 h-100 position-relative overflow-hidden" style="border-left: 5px solid #dc3545;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Pengeluaran Bulan Ini</span>
                <span class="bg-danger-subtle text-danger p-2 rounded-3">
                    <i class="bi bi-arrow-up-right fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-danger" id="counterExpense" data-target="{{ $currentMonthExpense }}">
                Rp 0
            </h4>
            <div class="small">
                <span class="text-secondary fw-semibold">{{ $expenseGrowth }}%</span>
                <span class="text-muted">vs bulan lalu</span>
            </div>
        </div>
    </div>

    <!-- Total Transaksi Bulan Ini -->
    <div class="col-sm-6 col-xl-3 anim-stat-4">
        <div class="card-custom p-3 h-100 position-relative overflow-hidden" style="border-left: 5px solid #0d6efd;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">Transaksi Bulan Ini</span>
                <span class="bg-info-subtle text-primary p-2 rounded-3">
                    <i class="bi bi-receipt fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark" id="counterTx" data-target="{{ $currentMonthTxCount }}">
                0 Transaksi
            </h4>
            <small class="text-muted d-block">Pencatatan kas terverifikasi</small>
        </div>
    </div>
</div>

<!-- 3. CHARTS SECTION -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Cash Flow Trends (Per Hari, Per Bulan, Per Tahun) -->
    <div class="col-lg-8 anim-chart-left">
        <div class="card-custom p-4 h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1" id="chartTitle">Tren Arus Kas (Per Bulan)</h5>
                    <small class="text-secondary" id="chartSubtitle">Perbandingan pemasukan (hijau) dan pengeluaran (merah) 12 bulan terakhir</small>
                </div>

                <!-- Toggle Switcher: Per Hari, Per Bulan, Per Tahun -->
                <div class="d-inline-flex bg-light p-1 rounded-pill border">
                    <button type="button" class="period-switch-btn" id="btnHarian" onclick="switchPeriod('harian')">
                        <i class="bi bi-calendar-day"></i> Per Hari
                    </button>
                    <button type="button" class="period-switch-btn active" id="btnBulanan" onclick="switchPeriod('bulanan')">
                        <i class="bi bi-calendar3"></i> Per Bulan
                    </button>
                    <button type="button" class="period-switch-btn" id="btnTahunan" onclick="switchPeriod('tahunan')">
                        <i class="bi bi-graph-up"></i> Per Tahun
                    </button>
                </div>
            </div>

            <!-- Dynamic Live Touch/Hover Inspection Card -->
            <div class="touch-inspector-card mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2" id="scrubInspector">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success text-white px-2 py-1" id="inspectMonthBadge">
                        {{ end($chartMonths) }}
                    </span>
                    <span class="small fw-semibold text-secondary" id="inspectLabel">Rincian Periode:</span>
                </div>
                <div class="d-flex align-items-center gap-3 small">
                    <div>
                        <span class="text-muted">Masuk:</span>
                        <strong class="text-success" id="inspectIncome">Rp {{ number_format(end($chartIncome), 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="text-muted">Keluar:</span>
                        <strong class="text-danger" id="inspectExpense">Rp {{ number_format(end($chartExpense), 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="text-muted">Surplus:</span>
                        <strong class="text-primary fw-bold" id="inspectNet">
                            Rp {{ number_format(end($chartIncome) - end($chartExpense), 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

            <div style="height: 310px; position: relative;">
                <canvas id="cashFlowChart"></canvas>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small text-muted">
                <span><i class="bi bi-info-circle me-1"></i> Sentuh atau arahkan kursor ke grafik untuk memeriksa detail</span>
                <span class="text-success fw-semibold"><i class="bi bi-arrow-up-circle me-1"></i>Batang Hijau & Merah Otomatis Naik ke Atas</span>
            </div>
        </div>
    </div>

    <!-- Chart 2: Expense by Category (Lingkaran Donut Merah Muter Penuh 360 Derajat) -->
    <div class="col-lg-4 anim-chart-right">
        <div class="card-custom p-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="fw-bold mb-0">Pengeluaran per Pos</h5>
                    <small class="text-secondary">Distribusi kategori bulan ini</small>
                </div>
            </div>

            @if(count($catLabels) > 0)
                <div style="height: 250px; position: relative; margin: auto 0;">
                    <canvas id="categoryChart"></canvas>
                </div>
                <!-- Interactive Pos Badges -->
                <div class="d-flex flex-wrap gap-1 mt-3 justify-content-center">
                    @foreach($catLabels as $idx => $catName)
                    <span class="cat-interactive-pill" onclick="highlightDonutSlice({{ $idx }})">
                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#dc3545;"></span>
                        {{ $catName }}
                    </span>
                    @endforeach
                </div>
                <div class="text-center mt-2 text-muted small">
                    <small><i class="bi bi-hand-index-thumb"></i> Klik nama pos untuk menyorot potongan donat</small>
                </div>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 text-center py-5">
                    <div class="bg-light rounded-circle p-3 mb-2">
                        <i class="bi bi-pie-chart text-muted fs-1"></i>
                    </div>
                    <p class="text-muted small mb-0">Belum ada data pengeluaran bulan ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- 4. RECENT TRANSACTIONS TABLE WITH HOVER & ACTION MODALS -->
<div class="card-custom p-4 mb-4 anim-table">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Transaksi Kas Terbaru</h5>
            <small class="text-secondary">6 pencatatan keuangan terakhir yang dimasukkan</small>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-success btn-sm fw-semibold">
            Lihat Semua Transaksi <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="small fw-bold text-secondary">TANGGAL</th>
                    <th scope="col" class="small fw-bold text-secondary">TIPE</th>
                    <th scope="col" class="small fw-bold text-secondary">KATEGORI / POS</th>
                    <th scope="col" class="small fw-bold text-secondary">KETERANGAN</th>
                    <th scope="col" class="small fw-bold text-secondary text-end">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $tx)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('transactions.index') }}'">
                    <td>
                        <span class="fw-semibold text-dark">{{ $tx->transaction_date->format('d M Y') }}</span>
                    </td>
                    <td>
                        @if($tx->type == 'pemasukan')
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                <i class="bi bi-arrow-down-left me-1"></i> Masuk
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">
                                <i class="bi bi-arrow-up-right me-1"></i> Keluar
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1">
                            <i class="bi bi-tag text-muted me-1"></i> {{ $tx->category->name ?? 'Tanpa Kategori' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-secondary">{{ Str::limit($tx->description, 45) }}</span>
                    </td>
                    <td class="text-end">
                        <span class="fw-bold {{ $tx->type == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-1 text-muted"></i>
                        Belum ada data transaksi kas yang dicatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- QUICK TRANSACTION MODAL -->
<div class="modal fade" id="quickTxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="quickTxTitle">Catat Kas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-3">
                    <input type="hidden" name="type" id="quickType" value="pemasukan">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jumlah Uang (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" name="amount" id="quickAmountInput" class="form-control form-control-lg fw-bold" placeholder="0" required min="1" oninput="updateRupiahPreview(this, 'quickAmountPreview')">
                        </div>
                        <div id="quickAmountPreview" class="small fw-semibold text-success mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kategori / Pos Kas <span class="text-danger">*</span></label>
                        <select name="category_id" id="quickCategorySelect" class="form-select" required>
                            <option value="">-- Pilih Pos Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan Transaksi <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" placeholder="Contoh: Penjualan toko, kulakan beras, bayar listrik" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan nomor bon, pelanggan, atau supplier..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Foto Bukti / Nota (Opsional)</label>
                        <input type="file" name="receipt_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted" style="font-size: 11px;">Maks. 3MB (JPG, PNG, WEBP)</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold" id="quickSubmitBtn">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // =========================================================================
    // 0. QUICK TRANSACTION MODAL HELPER
    // =========================================================================
    function openQuickTx(type) {
        document.getElementById('quickType').value = type;
        const titleEl = document.getElementById('quickTxTitle');
        const submitBtn = document.getElementById('quickSubmitBtn');
        const catSelect = document.getElementById('quickCategorySelect');

        if (type === 'pemasukan') {
            titleEl.innerHTML = '<span class="text-success"><i class="bi bi-arrow-down-left"></i> Tambah Pemasukan Kas</span>';
            submitBtn.className = 'btn btn-success rounded-pill px-4 fw-semibold';
            submitBtn.innerText = 'Simpan Pemasukan';
        } else {
            titleEl.innerHTML = '<span class="text-danger"><i class="bi bi-arrow-up-right"></i> Tambah Pengeluaran Kas</span>';
            submitBtn.className = 'btn btn-danger rounded-pill px-4 fw-semibold';
            submitBtn.innerText = 'Simpan Pengeluaran';
        }

        Array.from(catSelect.options).forEach(opt => {
            if (opt.value === "") return;
            const optType = opt.getAttribute('data-type');
            if (optType === type) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });

        const myModal = new bootstrap.Modal(document.getElementById('quickTxModal'));
        myModal.show();
    }

    // =========================================================================
    // 1. LIVE NUMBER COUNTUP ANIMATION FOR STATISTIC CARDS
    // =========================================================================
    function animateValue(obj, start, end, duration, isCurrency = true) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            // Ease out quad
            const easeProgress = 1 - (1 - progress) * (1 - progress);
            const currentVal = Math.floor(easeProgress * (end - start) + start);

            if (isCurrency) {
                obj.innerHTML = 'Rp ' + currentVal.toLocaleString('id-ID');
            } else {
                obj.innerHTML = currentVal + ' Transaksi';
            }

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                if (isCurrency) {
                    obj.innerHTML = 'Rp ' + end.toLocaleString('id-ID');
                } else {
                    obj.innerHTML = end + ' Transaksi';
                }
            }
        };
        window.requestAnimationFrame(step);
    }

    window.addEventListener('DOMContentLoaded', () => {
        const balEl = document.getElementById('counterBalance');
        const incEl = document.getElementById('counterIncome');
        const expEl = document.getElementById('counterExpense');
        const txEl = document.getElementById('counterTx');

        if (balEl) animateValue(balEl, 0, parseInt(balEl.dataset.target || 0), 1200, true);
        if (incEl) animateValue(incEl, 0, parseInt(incEl.dataset.target || 0), 1200, true);
        if (expEl) animateValue(expEl, 0, parseInt(expEl.dataset.target || 0), 1200, true);
        if (txEl) animateValue(txEl, 0, parseInt(txEl.dataset.target || 0), 900, false);
    });

    // =========================================================================
    // 2. TREN ARUS KAS: PER HARI, PER BULAN, PER TAHUN DENGAN ANIMASI BATANG NAIK
    // =========================================================================
    const dailyLabels = {!! json_encode($dailyLabels) !!};
    const dailyIncome = {!! json_encode($dailyIncome) !!};
    const dailyExpense = {!! json_encode($dailyExpense) !!};

    const monthsData = {!! json_encode($chartMonths) !!};
    const incomeData = {!! json_encode($chartIncome) !!};
    const expenseData = {!! json_encode($chartExpense) !!};

    const yearlyLabels = {!! json_encode($yearlyLabels) !!};
    const yearlyIncome = {!! json_encode($yearlyIncome) !!};
    const yearlyExpense = {!! json_encode($yearlyExpense) !!};

    let activePeriod = 'bulanan'; // default: 'bulanan', options: 'harian', 'bulanan', 'tahunan'
    let currentLabels = monthsData;
    let currentIncome = incomeData;
    let currentExpense = expenseData;

    const ctxCashFlow = document.getElementById('cashFlowChart').getContext('2d');

    let cashFlowChart = new Chart(ctxCashFlow, {
        type: 'bar',
        data: {
            labels: currentLabels,
            datasets: [
                {
                    label: 'Pemasukan (Masuk)',
                    data: currentIncome,
                    backgroundColor: '#198754',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    categoryPercentage: 0.75,
                },
                {
                    label: 'Pengeluaran (Keluar)',
                    data: currentExpense,
                    backgroundColor: '#dc3545',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    categoryPercentage: 0.75,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // ANIMASI BATANG HIJAU DAN MERAH NAIK KE ATAS DARI BASELINE (y=0)
            animations: {
                y: {
                    type: 'number',
                    easing: 'easeOutQuart',
                    duration: 1500,
                    from: (ctx) => {
                        if (ctx.type === 'data') {
                            return ctx.chart.scales.y.getPixelForValue(0);
                        }
                    },
                    delay: (ctx) => {
                        let stepDelay = 60;
                        if (activePeriod === 'harian') stepDelay = 22;
                        else if (activePeriod === 'tahunan') stepDelay = 120;
                        return (ctx.dataIndex * stepDelay) + (ctx.datasetIndex * 100);
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            onHover: (event, chartElements) => {
                if (chartElements && chartElements.length > 0) {
                    const idx = chartElements[0].index;
                    updateScrubInspector(idx);
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 10,
                        font: { size: 12, family: "'Poppins', sans-serif" }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.92)',
                    titleFont: { size: 13, family: "'Poppins', sans-serif", weight: 'bold' },
                    bodyFont: { size: 12, family: "'Poppins', sans-serif" },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                        },
                        afterBody: function(items) {
                            if (items.length >= 2) {
                                const inc = items[0].raw;
                                const exp = items[1].raw;
                                const diff = inc - exp;
                                return '\n Selisih/Surplus: ' + (diff >= 0 ? '+' : '') + 'Rp ' + diff.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, family: "'Poppins', sans-serif" },
                        color: '#64748b',
                        maxRotation: activePeriod === 'harian' ? 45 : 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: {
                        font: { size: 11, family: "'Poppins', sans-serif" },
                        color: '#64748b',
                        callback: function(value) {
                            if (value >= 1000000000) {
                                return 'Rp ' + (value / 1000000000).toLocaleString('id-ID') + ' M';
                            }
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                            }
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' rb';
                        }
                    }
                }
            }
        }
    });

    // Update touch inspection bar
    function updateScrubInspector(idx) {
        if (idx < 0 || idx >= currentLabels.length) return;
        const label = currentLabels[idx];
        const inc = currentIncome[idx] || 0;
        const exp = currentExpense[idx] || 0;
        const net = inc - exp;

        document.getElementById('inspectMonthBadge').innerText = label;
        document.getElementById('inspectIncome').innerText = 'Rp ' + inc.toLocaleString('id-ID');
        document.getElementById('inspectExpense').innerText = 'Rp ' + exp.toLocaleString('id-ID');
        
        const netEl = document.getElementById('inspectNet');
        netEl.innerText = (net >= 0 ? '+Rp ' : '-Rp ') + Math.abs(net).toLocaleString('id-ID');
        netEl.className = net >= 0 ? 'text-primary fw-bold' : 'text-danger fw-bold';
    }

    // Switch between Per Hari, Per Bulan, and Per Tahun
    function switchPeriod(mode) {
        activePeriod = mode;
        const btnHarian = document.getElementById('btnHarian');
        const btnBulanan = document.getElementById('btnBulanan');
        const btnTahunan = document.getElementById('btnTahunan');
        const titleEl = document.getElementById('chartTitle');
        const subTitleEl = document.getElementById('chartSubtitle');

        btnHarian.classList.remove('active');
        btnBulanan.classList.remove('active');
        btnTahunan.classList.remove('active');

        if (mode === 'harian') {
            btnHarian.classList.add('active');
            titleEl.innerText = 'Tren Arus Kas (Per Hari)';
            subTitleEl.innerText = 'Perbandingan pemasukan (hijau) dan pengeluaran (merah) 30 hari terakhir';

            currentLabels = dailyLabels;
            currentIncome = dailyIncome;
            currentExpense = dailyExpense;

            cashFlowChart.options.scales.x.ticks.maxRotation = 45;
        } else if (mode === 'bulanan') {
            btnBulanan.classList.add('active');
            titleEl.innerText = 'Tren Arus Kas (Per Bulan)';
            subTitleEl.innerText = 'Perbandingan pemasukan (hijau) dan pengeluaran (merah) 12 bulan terakhir';

            currentLabels = monthsData;
            currentIncome = incomeData;
            currentExpense = expenseData;

            cashFlowChart.options.scales.x.ticks.maxRotation = 0;
        } else if (mode === 'tahunan') {
            btnTahunan.classList.add('active');
            titleEl.innerText = 'Tren Arus Kas (Per Tahun)';
            subTitleEl.innerText = 'Perbandingan pemasukan (hijau) dan pengeluaran (merah) 5 tahun terakhir';

            currentLabels = yearlyLabels;
            currentIncome = yearlyIncome;
            currentExpense = yearlyExpense;

            cashFlowChart.options.scales.x.ticks.maxRotation = 0;
        }

        cashFlowChart.data.labels = currentLabels;
        cashFlowChart.data.datasets[0].data = currentIncome;
        cashFlowChart.data.datasets[1].data = currentExpense;

        // Reset and trigger smooth rising animation
        cashFlowChart.reset();
        cashFlowChart.update();

        // Update inspector to last item
        updateScrubInspector(currentLabels.length - 1);
    }

    // =========================================================================
    // 3. EXPENSE DONUT: WARNA MERAH & IRISAN POS MUTER PENUH 360 DERAJAT
    // =========================================================================
    @if(count($catLabels) > 0)
    const catLabels = {!! json_encode($catLabels) !!};
    const catValues = {!! json_encode($catValues) !!};
    const catTotal = catValues.reduce((a, b) => a + b, 0);

    const donutCenterTextPlugin = {
        id: 'donutCenterText',
        beforeDraw(chart) {
            const { ctx, chartArea: { top, width, height } } = chart;
            ctx.save();

            const textTitle = chart.config.options.plugins.centerTitle || 'Total Pengeluaran';
            const textAmount = chart.config.options.plugins.centerAmount || ('Rp ' + (catTotal / 1000).toLocaleString('id-ID') + 'k');

            const centerX = width / 2;
            const centerY = top + (height / 2);

            ctx.font = "500 11px 'Poppins', sans-serif";
            ctx.fillStyle = '#64748b';
            ctx.textAlign = 'center';
            ctx.fillText(textTitle, centerX, centerY - 8);

            ctx.font = "bold 15px 'Poppins', sans-serif";
            ctx.fillStyle = '#dc3545';
            ctx.textAlign = 'center';
            ctx.fillText(textAmount, centerX, centerY + 14);

            ctx.restore();
        }
    };

    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    const redPalette = [
        '#dc3545', '#e03131', '#c92a2a', '#b02a37', '#fa5252', '#ff6b6b', '#842029', '#a71d2a'
    ];

    let categoryChart = new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catValues,
                backgroundColor: redPalette.slice(0, catLabels.length),
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverBorderColor: '#ffffff',
                hoverBorderWidth: 5,
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            rotation: -450,
            circumference: 360,
            animation: {
                animateScale: true,
                duration: 0
            },
            onHover: (event, elements) => {
                if (elements && elements.length > 0) {
                    const idx = elements[0].index;
                    const name = catLabels[idx];
                    const val = catValues[idx];
                    const pct = Math.round((val / catTotal) * 100);

                    categoryChart.config.options.plugins.centerTitle = name + ' (' + pct + '%)';
                    categoryChart.config.options.plugins.centerAmount = 'Rp ' + val.toLocaleString('id-ID');
                } else {
                    categoryChart.config.options.plugins.centerTitle = 'Total Pengeluaran';
                    categoryChart.config.options.plugins.centerAmount = 'Rp ' + (catTotal / 1000).toLocaleString('id-ID') + 'k';
                }
                categoryChart.draw();
            },
            plugins: {
                centerTitle: 'Total Pengeluaran',
                centerAmount: 'Rp ' + (catTotal / 1000).toLocaleString('id-ID') + 'k',
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.92)',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const val = context.raw;
                            const pct = Math.round((val / catTotal) * 100);
                            return ' Rp ' + val.toLocaleString('id-ID') + ' (' + pct + '%)';
                        }
                    }
                }
            }
        },
        plugins: [donutCenterTextPlugin]
    });

    // MUTER PENUH 360 DERAJAT IRISAN MERAH
    function startFull360Spin() {
        let startTime = null;
        const totalDuration = 2000;
        const initialAngle = -450;
        const targetAngle = -90;

        function stepSpin(timestamp) {
            if (!startTime) startTime = timestamp;
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / totalDuration, 1);

            const ease = 1 - Math.pow(1 - progress, 4);
            const currentAngle = initialAngle + (360 * ease);

            categoryChart.options.rotation = currentAngle;
            categoryChart.update('none');

            if (progress < 1) {
                requestAnimationFrame(stepSpin);
            } else {
                categoryChart.options.rotation = targetAngle;
                categoryChart.update('none');
            }
        }

        requestAnimationFrame(stepSpin);
    }

    setTimeout(() => {
        startFull360Spin();
    }, 250);

    // Highlight slice when pill is clicked/hovered
    function highlightDonutSlice(index) {
        if (!categoryChart) return;
        
        const pills = document.querySelectorAll('.cat-interactive-pill');
        pills.forEach((p, idx) => {
            if (idx === index) p.classList.add('active');
            else p.classList.remove('active');
        });

        const name = catLabels[index];
        const val = catValues[index];
        const pct = Math.round((val / catTotal) * 100);
        categoryChart.config.options.plugins.centerTitle = name + ' (' + pct + '%)';
        categoryChart.config.options.plugins.centerAmount = 'Rp ' + val.toLocaleString('id-ID');

        categoryChart.setActiveElements([{ datasetIndex: 0, index: index }]);
        categoryChart.tooltip.setActiveElements([{ datasetIndex: 0, index: index }], { x: 0, y: 0 });
        categoryChart.update();
    }
    @endif
</script>
@endsection