@extends('layouts.app')

@section('title', 'Pencatatan Transaksi Kas')
@section('page_title', 'Pencatatan Transaksi Kas')

@section('content')

<!-- FILTER & SEARCH CARD -->
<div class="card-custom p-4 mb-4">
    <form action="{{ route('transactions.index') }}" method="GET" class="row g-3 align-items-end" id="filterForm">
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-secondary">Cari Transaksi</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Keterangan / catatan..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Jenis Kas</label>
            <select name="type" class="form-select">
                <option value="">Semua Jenis</option>
                <option value="pemasukan" {{ request('type') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('type') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Status Pembayaran</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="belum_lunas" {{ request('status') === 'belum_lunas' ? 'selected' : '' }}>Belum Dibayar (Tagihan)</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Kategori</label>
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} ({{ ucfirst($cat->type) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Mulai Dari</label>
            <input type="date" name="start_date" id="filterStartDate" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary">Sampai Tanggal</label>
            <input type="date" name="end_date" id="filterEndDate" class="form-control" value="{{ request('end_date') }}">
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-kaskita w-100" title="Terapkan Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->anyFilled(['search', 'type', 'status', 'category_id', 'start_date', 'end_date']))
                <a href="{{ route('transactions.index') }}" class="btn btn-light border" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>

        <!-- Quick Filter Preset Buttons -->
        <div class="col-12 pt-2 border-top d-flex flex-wrap align-items-center gap-2 small text-secondary">
            <span>Filter Cepat:</span>
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill" onclick="applyDatePreset('today')">Hari Ini</button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill" onclick="applyDatePreset('7days')">7 Hari Terakhir</button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill" onclick="applyDatePreset('month')">Bulan Ini</button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill" onclick="applyDatePreset('all')">Hapus Tanggal</button>
            <a href="{{ route('transactions.index', ['status' => 'belum_lunas']) }}" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill ms-auto fw-semibold">
                <i class="bi bi-exclamation-circle me-1"></i>Lihat Belum Dibayar
            </a>
        </div>
    </form>
</div>

<!-- FILTER SUMMARY BANNER -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-3 border-start border-success border-4 bg-white">
            <small class="text-secondary d-block">Pemasukan Terfilter</small>
            <h5 class="fw-bold text-success mb-0">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 border-start border-danger border-4 bg-white">
            <small class="text-secondary d-block">Pengeluaran Terfilter</small>
            <h5 class="fw-bold text-danger mb-0">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 border-start border-primary border-4 bg-white">
            <small class="text-secondary d-block">Selisih Bersih</small>
            <h5 class="fw-bold {{ $netFiltered >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                Rp {{ number_format($netFiltered, 0, ',', '.') }}
            </h5>
        </div>
    </div>
</div>

<!-- TRANSACTIONS LIST TABLE -->
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Transaksi Usaha</h5>
            <small class="text-secondary">Menampilkan {{ $transactions->total() }} catatan kas</small>
        </div>
        <button class="btn btn-kaskita btn-sm px-3" data-bs-toggle="modal" data-bs-target="#globalTransactionModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-secondary">
                <tr>
                    <th>TANGGAL</th>
                    <th>JENIS</th>
                    <th>STATUS & TANGGAL BAYAR</th>
                    <th>KATEGORI</th>
                    <th>KETERANGAN & CATATAN</th>
                    <th class="text-center">BUKTI / NOTA</th>
                    <th class="text-end">NOMINAL</th>
                    <th class="text-center" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                @php
                    $isUnpaid = ($tx->status === 'belum_lunas');
                    $dueDate = $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->startOfDay() : null;
                    $isOverdue = $isUnpaid && $dueDate && $dueDate->lt(\Carbon\Carbon::today());
                @endphp
                <tr class="{{ $isOverdue ? 'table-danger' : ($isUnpaid ? 'bg-light' : '') }}">
                    <td class="small fw-semibold text-dark">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                    <td>
                        @if($tx->type === 'pemasukan')
                            <span class="badge badge-income rounded-pill px-2 py-1">
                                <i class="bi bi-arrow-down-left"></i> Pemasukan
                            </span>
                        @else
                            <span class="badge badge-expense rounded-pill px-2 py-1">
                                <i class="bi bi-arrow-up-right"></i> Pengeluaran
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($isUnpaid)
                            <span class="badge bg-danger text-white rounded-pill px-2 py-1">
                                <i class="bi bi-clock-history me-1"></i> Belum Dibayar
                            </span>
                            @if($dueDate)
                                <div class="small fw-semibold {{ $isOverdue ? 'text-danger' : 'text-primary' }} mt-1">
                                    <i class="bi bi-calendar-event me-1"></i>Bayar: {{ $dueDate->format('d/m/Y') }}
                                </div>
                            @endif
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i> Lunas
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $tx->category->name ?? 'Tanpa Kategori' }}
                        </span>
                    </td>
                    <td>
                        <strong class="d-block text-dark">{{ $tx->description }}</strong>
                        @if($tx->notes)
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>{{ $tx->notes }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($tx->receipt_image)
                            <button type="button" class="btn btn-sm btn-outline-success px-2 py-1 rounded-pill" onclick="viewReceipt('{{ asset('storage/' . $tx->receipt_image) }}', '{{ addslashes($tx->description) }}')" title="Lihat Bukti Transaksi">
                                <i class="bi bi-image me-1"></i> Lihat
                            </button>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold {{ $tx->type === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                        {{ $tx->type === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if($isUnpaid)
                                <form action="{{ route('transactions.mark-paid', $tx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai tagihan \'{{ addslashes($tx->description) }}\' sebesar Rp {{ number_format($tx->amount, 0, ',', '.') }} sebagai SUDAH LUNAS?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Tandai Sudah Dibayar">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            @endif
                            <button class="btn btn-outline-secondary" onclick="editTransaction({{ json_encode($tx) }})" title="Edit Transaksi">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Hapus catatan transaksi ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        Tidak ada transaksi kas yang sesuai kriteria pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- EDIT TRANSACTION MODAL -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="editTxForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Transaksi Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Jenis Transaksi</label>
                        <select name="type" class="form-select" id="editType" required onchange="filterEditCategories(this.value, true)">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control form-control-lg fw-bold" id="editAmount" required min="1" oninput="updateRupiahPreview(this, 'editAmountPreview')">
                        <div id="editAmountPreview" class="small fw-semibold text-success mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Kategori</label>
                        <select name="category_id" class="form-select" id="editCategory">
                            <option value="">-- Tanpa Kategori / Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" id="editDate" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Status Pembayaran <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" id="editStatus" onchange="toggleEditDueDate(this.value === 'belum_lunas')">
                            <option value="lunas">Lunas (Sudah Dibayar)</option>
                            <option value="belum_lunas">Belum Dibayar (Tagihan / Tempo)</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="editDueDateContainer">
                        <label class="form-label fw-semibold small text-danger">
                            <i class="bi bi-calendar-event me-1"></i> Tanggal Bayar / Jatuh Tempo
                        </label>
                        <input type="date" name="due_date" class="form-control border-warning bg-light" id="editDueDate">
                        <small class="text-muted" style="font-size: 11px;">Pengingat batas bayar otomatis masuk ke menu Notifikasi & dashboard.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Keterangan Transaksi <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" id="editDescription" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Catatan Tambahan</label>
                        <textarea name="notes" class="form-control" rows="2" id="editNotes"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Foto Bukti / Struk (Opsional)</label>
                        <input type="file" name="receipt_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <div id="currentReceiptContainer" class="mt-2 d-none">
                            <span class="small text-secondary d-block mb-1">Bukti yang tersimpan saat ini:</span>
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" id="currentReceiptLink" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Buka Gambar
                                </a>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="checkbox" name="remove_receipt" id="removeReceiptCheckbox" value="1">
                                    <label class="form-check-label small text-danger" for="removeReceiptCheckbox">Hapus Bukti</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-kaskita px-4">Perbarui Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- RECEIPT PREVIEW LIGHTBOX MODAL -->
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="receiptPreviewTitle">Bukti Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img src="" id="receiptPreviewImg" class="img-fluid rounded-3 shadow-sm border" style="max-height: 70vh;" alt="Bukti Transaksi">
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="" id="receiptDownloadBtn" download class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i> Unduh Gambar
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function filterEditCategories(type, resetValue = false) {
    const select = document.getElementById('editCategory');
    if (!select) return;
    const options = select.querySelectorAll('option');
    options.forEach(opt => {
        if (opt.value === '') return;
        if (opt.getAttribute('data-type') === type) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
    if (resetValue) {
        select.value = '';
    }
}

function toggleEditDueDate(isUnpaid) {
    const container = document.getElementById('editDueDateContainer');
    if (!container) return;
    if (isUnpaid) {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}

function editTransaction(tx) {
    document.getElementById('editTxForm').action = "{{ url('transactions') }}/" + tx.id;
    document.getElementById('editType').value = tx.type;
    filterEditCategories(tx.type, false);
    document.getElementById('editCategory').value = tx.category_id || '';
    document.getElementById('editDate').value = tx.transaction_date.substring(0, 10);
    document.getElementById('editDescription').value = tx.description;
    document.getElementById('editNotes').value = tx.notes || '';

    const statusVal = tx.status || 'lunas';
    const editStatusEl = document.getElementById('editStatus');
    if (editStatusEl) editStatusEl.value = statusVal;
    const editDueDateEl = document.getElementById('editDueDate');
    if (editDueDateEl) editDueDateEl.value = tx.due_date ? tx.due_date.substring(0, 10) : '';
    toggleEditDueDate(statusVal === 'belum_lunas');

    const receiptCont = document.getElementById('currentReceiptContainer');
    const receiptLink = document.getElementById('currentReceiptLink');
    const removeCheck = document.getElementById('removeReceiptCheckbox');
    if (removeCheck) removeCheck.checked = false;

    if (tx.receipt_image) {
        receiptLink.href = "{{ asset('storage') }}/" + tx.receipt_image;
        receiptCont.classList.remove('d-none');
    } else {
        receiptCont.classList.add('d-none');
    }

    if (typeof updateRupiahPreview === 'function') {
        updateRupiahPreview(document.getElementById('editAmount'), 'editAmountPreview');
    }

    var modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
    modal.show();
}

function viewReceipt(imageUrl, desc) {
    document.getElementById('receiptPreviewImg').src = imageUrl;
    document.getElementById('receiptPreviewTitle').textContent = 'Bukti: ' + desc;
    document.getElementById('receiptDownloadBtn').href = imageUrl;

    var modal = new bootstrap.Modal(document.getElementById('receiptPreviewModal'));
    modal.show();
}

function applyDatePreset(preset) {
    const startInput = document.getElementById('filterStartDate');
    const endInput = document.getElementById('filterEndDate');
    const today = new Date();
    
    function fmt(d) {
        return d.toISOString().split('T')[0];
    }

    if (preset === 'today') {
        startInput.value = fmt(today);
        endInput.value = fmt(today);
    } else if (preset === '7days') {
        const past7 = new Date();
        past7.setDate(today.getDate() - 6);
        startInput.value = fmt(past7);
        endInput.value = fmt(today);
    } else if (preset === 'month') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        startInput.value = fmt(firstDay);
        endInput.value = fmt(lastDay);
    } else if (preset === 'all') {
        startInput.value = '';
        endInput.value = '';
    }
    document.getElementById('filterForm').submit();
}
</script>
@endsection