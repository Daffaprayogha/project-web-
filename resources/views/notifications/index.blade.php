@extends('layouts.app')

@section('title', 'Notifikasi & Pengingat Tagihan')
@section('page_title', 'Notifikasi & Pengingat Tagihan')

@section('content')

@php
    $today = \Carbon\Carbon::today();
    $totalUnpaidAmount = $unpaidBills->sum('amount');
    $overdueCount = $unpaidBills->filter(fn($b) => $b->due_date && \Carbon\Carbon::parse($b->due_date)->startOfDay()->lt($today))->count();
    $dueTodayCount = $unpaidBills->filter(fn($b) => $b->due_date && \Carbon\Carbon::parse($b->due_date)->startOfDay()->isToday())->count();
@endphp

<!-- UNPAID BILLS SUMMARY ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-3 border-start border-danger border-4 bg-white d-flex align-items-center justify-content-between">
            <div>
                <small class="text-secondary d-block">Total Tagihan Belum Dibayar</small>
                <h5 class="fw-bold text-danger mb-0">Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}</h5>
            </div>
            <div class="p-2 rounded-circle bg-danger-subtle text-danger fs-4">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 border-start border-warning border-4 bg-white d-flex align-items-center justify-content-between">
            <div>
                <small class="text-secondary d-block">Jatuh Tempo / Lewat Batas</small>
                <h5 class="fw-bold text-dark mb-0">
                    <span class="text-danger">{{ $overdueCount }} Telat</span> &bull; 
                    <span class="text-warning text-dark">{{ $dueTodayCount }} Hari Ini</span>
                </h5>
            </div>
            <div class="p-2 rounded-circle bg-warning-subtle text-warning fs-4">
                <i class="bi bi-calendar-event"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 border-start border-success border-4 bg-white d-flex align-items-center justify-content-between">
            <div>
                <small class="text-secondary d-block">Status Keuangan</small>
                <h5 class="fw-bold text-success mb-0">{{ $unpaidBills->count() }} Item Tertunda</h5>
            </div>
            <button class="btn btn-kaskita btn-sm px-3" data-bs-toggle="modal" data-bs-target="#globalTransactionModal" onclick="prepareUnpaidBillModal()">
                <i class="bi bi-plus-lg me-1"></i> Catat Tagihan
            </button>
        </div>
    </div>
</div>

<!-- SECTION 1: DAFTAR PENGELUARAN BELUM DIBAYAR & TANGGAL BAYAR -->
<div class="card-custom p-4 mb-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>Daftar Tagihan & Pengeluaran Belum Dibayar
            </h5>
            <small class="text-secondary">Pantau tagihan yang harus segera dilunasi beserta tanggal jatuh tempo pembayaran</small>
        </div>
        <a href="{{ route('transactions.index', ['status' => 'belum_lunas']) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right-circle me-1"></i> Lihat di Transaksi
        </a>
    </div>

    @if($unpaidBills->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th>KATEGORI</th>
                        <th>KETERANGAN TAGIHAN</th>
                        <th>NOMINAL</th>
                        <th>TANGGAL BAYAR / JATUH TEMPO</th>
                        <th>STATUS TEMPO</th>
                        <th class="text-center" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unpaidBills as $bill)
                    @php
                        $dueDate = $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->startOfDay() : null;
                        $isOverdue = $dueDate && $dueDate->lt($today);
                        $isDueToday = $dueDate && $dueDate->isToday();
                        $daysDiff = $dueDate ? $today->diffInDays($dueDate, false) : null;
                    @endphp
                    <tr class="{{ $isOverdue ? 'table-danger' : ($isDueToday ? 'table-warning' : '') }}">
                        <td>
                            @if($bill->category)
                                <span class="badge rounded-pill text-white px-3 py-2" style="background-color: {{ $bill->category->color ?? '#6c757d' }}">
                                    <i class="bi {{ $bill->category->icon ?? 'bi-tag' }} me-1"></i>{{ $bill->category->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3 py-2">Pengeluaran Umum</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $bill->description }}</strong>
                            @if($bill->notes)
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>{{ $bill->notes }}</small>
                            @endif
                            <small class="text-secondary d-block" style="font-size: 11px;">Dicatat: {{ $bill->transaction_date->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <h6 class="fw-bold text-danger mb-0">Rp {{ number_format($bill->amount, 0, ',', '.') }}</h6>
                        </td>
                        <td>
                            @if($dueDate)
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>{{ $dueDate->format('d M Y') }}
                                </div>
                            @else
                                <span class="text-muted small">Belum ditentukan</span>
                            @endif
                        </td>
                        <td>
                            @if(!$dueDate)
                                <span class="badge bg-secondary-subtle text-secondary border">Belum Ada Tanggal</span>
                            @elseif($isOverdue)
                                <span class="badge bg-danger text-white py-1 px-2 shadow-sm">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Terlewat {{ abs($daysDiff) }} Hari!
                                </span>
                            @elseif($isDueToday)
                                <span class="badge bg-warning text-dark py-1 px-2 shadow-sm fw-bold">
                                    <i class="bi bi-bell-fill me-1"></i>Jatuh Tempo Hari Ini!
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-primary border py-1 px-2">
                                    <i class="bi bi-clock me-1"></i>{{ $daysDiff }} Hari Lagi
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('transactions.mark-paid', $bill->id) }}" method="POST" onsubmit="return confirm('Tandai tagihan \'{{ addslashes($bill->description) }}\' sebesar Rp {{ number_format($bill->amount, 0, ',', '.') }} sebagai SUDAH LUNAS?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm fw-semibold w-100 shadow-sm" title="Tandai Sudah Dibayar">
                                    <i class="bi bi-check-circle-fill me-1"></i> Tandai Lunas
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4 bg-light rounded-3">
            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
            <h6 class="fw-bold text-dark mb-1">Semua Pengeluaran & Tagihan Lunas!</h6>
            <p class="text-secondary small mb-3">Tidak ada tagihan tertunda yang belum dibayar saat ini.</p>
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#globalTransactionModal" onclick="prepareUnpaidBillModal()">
                <i class="bi bi-plus-circle me-1"></i> Catat Tagihan Baru
            </button>
        </div>
    @endif
</div>

<!-- SECTION 2: RIWAYAT NOTIFIKASI SISTEM -->
<div class="card-custom p-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-bell-fill text-primary me-2"></i>Riwayat Notifikasi Sistem
            </h5>
            <small class="text-secondary">Pesan pengingat batas bayar, aktivitas kas, dan konfirmasi pelunasan</small>
        </div>
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-check-all me-1"></i> Tandai Semua Dibaca
            </button>
        </form>
    </div>

    <div class="list-group list-group-flush">
        @forelse($notifications as $notif)
        <div class="list-group-item d-flex justify-content-between align-items-start px-3 py-3 rounded-3 mb-2 border {{ $notif->is_read ? 'bg-white opacity-75' : 'bg-light border-start border-4 border-primary shadow-sm' }}">
            <div class="d-flex align-items-start gap-3">
                <span class="p-2 rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" 
                      style="background-color: {{ $notif->type === 'success' ? '#198754' : ($notif->type === 'warning' ? '#fd7e14' : ($notif->type === 'danger' ? '#dc3545' : '#0d6efd')) }}; width: 38px; height: 38px;">
                    @if($notif->type === 'success')
                        <i class="bi bi-check2 fs-5"></i>
                    @elseif($notif->type === 'warning')
                        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    @elseif($notif->type === 'danger')
                        <i class="bi bi-x-circle-fill fs-6"></i>
                    @else
                        <i class="bi bi-bell-fill fs-6"></i>
                    @endif
                </span>
                <div>
                    <strong class="text-dark d-block mb-1">{{ $notif->title }}</strong>
                    <p class="text-secondary small mb-1">{{ $notif->message }}</p>
                    <div class="d-flex align-items-center gap-3">
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</small>
                        @if($notif->transaction && $notif->transaction->status === 'belum_lunas')
                            <form action="{{ route('transactions.mark-paid', $notif->transaction_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai tagihan ini sebagai lunas?')">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm text-success p-0 fw-semibold text-decoration-none">
                                    <i class="bi bi-check-circle me-1"></i>Bayar Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @if(!$notif->is_read)
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary py-1 px-2 text-decoration-none small">
                        Tandai dibaca
                    </button>
                </form>
            @endif
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
            Belum ada notifikasi baru untuk Anda.
        </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection

@section('scripts')
<script>
function prepareUnpaidBillModal() {
    // Select expense
    const expenseRadio = document.getElementById('typeExpense');
    if (expenseRadio) {
        expenseRadio.checked = true;
        if (typeof filterCategories === 'function') {
            filterCategories('pengeluaran');
        }
    }
    // Select belum_lunas
    const statusUnpaidRadio = document.getElementById('globalStatusUnpaid');
    if (statusUnpaidRadio) {
        statusUnpaidRadio.checked = true;
        toggleDueDateContainer('global', true);
    }
    // Default due date to 3 days ahead
    const dueDateInput = document.getElementById('globalDueDateInput');
    if (dueDateInput && !dueDateInput.value) {
        const d = new Date();
        d.setDate(d.getDate() + 3);
        dueDateInput.value = d.toISOString().split('T')[0];
    }
}
</script>
@endsection