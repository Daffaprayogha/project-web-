@extends('layouts.app')

@section('title', 'Manajemen Kategori Transaksi')
@section('page_title', 'Manajemen Kategori Transaksi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Pos Kategori Transaksi</h5>
        <small class="text-secondary">Kelompokkan pos pemasukan dan pengeluaran sesuai kebutuhan usaha Anda</small>
    </div>
    <button class="btn btn-kaskita btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori Baru
    </button>
</div>

<div class="row g-4">
    <!-- Kategori Pemasukan -->
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="bg-success-subtle text-success p-2 rounded-3">
                        <i class="bi bi-arrow-down-left fs-5"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-success">Kategori Pemasukan</h5>
                </div>
                <span class="badge bg-success-subtle text-success">{{ count($incomeCategories) }} Pos</span>
            </div>

            <div class="d-grid gap-2">
                @forelse($incomeCategories as $cat)
                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="background-color: {{ $cat->color }}; width: 38px; height: 38px;">
                            <i class="bi {{ $cat->icon }}"></i>
                        </span>
                        <div>
                            <strong class="text-dark d-block">{{ $cat->name }}</strong>
                            <small class="text-muted">{{ $cat->transactions_count }} transaksi &bull; Total: Rp {{ number_format($cat->transactions_sum_amount ?? 0, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light btn-sm border" onclick="editCat({{ json_encode($cat) }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light text-danger btn-sm border" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">Belum ada kategori pemasukan.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Kategori Pengeluaran -->
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="bg-danger-subtle text-danger p-2 rounded-3">
                        <i class="bi bi-arrow-up-right fs-5"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-danger">Kategori Pengeluaran</h5>
                </div>
                <span class="badge bg-danger-subtle text-danger">{{ count($expenseCategories) }} Pos</span>
            </div>

            <div class="d-grid gap-2">
                @forelse($expenseCategories as $cat)
                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="background-color: {{ $cat->color }}; width: 38px; height: 38px;">
                            <i class="bi {{ $cat->icon }}"></i>
                        </span>
                        <div>
                            <strong class="text-dark d-block">{{ $cat->name }}</strong>
                            <small class="text-muted">{{ $cat->transactions_count }} transaksi &bull; Total: Rp {{ number_format($cat->transactions_sum_amount ?? 0, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light btn-sm border" onclick="editCat({{ json_encode($cat) }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light text-danger btn-sm border" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">Belum ada kategori pengeluaran.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD CATEGORY -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Jenis Pos</label>
                        <select name="type" class="form-select" required>
                            <option value="pengeluaran">Pengeluaran</option>
                            <option value="pemasukan">Pemasukan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Belanja Bahan Baku / Promosi Sosmed" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Ikon</label>
                            <select name="icon" class="form-select">
                                <option value="bi-tag">Tag Label</option>
                                <option value="bi-shop">Toko / Outlet</option>
                                <option value="bi-box-seam">Barang / Stok</option>
                                <option value="bi-cash-coin">Uang Tunai</option>
                                <option value="bi-wallet2">Dompet Kas</option>
                                <option value="bi-lightning-charge">Listrik & Energi</option>
                                <option value="bi-people">Karyawan</option>
                                <option value="bi-building">Gedung / Sewa</option>
                                <option value="bi-cart">Belanjaan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Warna Pos</label>
                            <select name="color" class="form-select">
                                <option value="#198754">Hijau Utama</option>
                                <option value="#dc3545">Merah Pengeluaran</option>
                                <option value="#0d6efd">Biru Info</option>
                                <option value="#fd7e14">Oranye</option>
                                <option value="#6f42c1">Ungu</option>
                                <option value="#20c997">Toska</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-kaskita px-4">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT CATEGORY -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="editCatForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Jenis Pos</label>
                        <select name="type" class="form-select" id="editCatType" required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" id="editCatName" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Ikon</label>
                            <select name="icon" class="form-select" id="editCatIcon">
                                <option value="bi-tag">Tag Label</option>
                                <option value="bi-shop">Toko / Outlet</option>
                                <option value="bi-box-seam">Barang / Stok</option>
                                <option value="bi-cash-coin">Uang Tunai</option>
                                <option value="bi-wallet2">Dompet Kas</option>
                                <option value="bi-lightning-charge">Listrik & Energi</option>
                                <option value="bi-people">Karyawan</option>
                                <option value="bi-building">Gedung / Sewa</option>
                                <option value="bi-cart">Belanjaan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Warna Pos</label>
                            <select name="color" class="form-select" id="editCatColor">
                                <option value="#198754">Hijau Utama</option>
                                <option value="#dc3545">Merah Pengeluaran</option>
                                <option value="#0d6efd">Biru Info</option>
                                <option value="#fd7e14">Oranye</option>
                                <option value="#6f42c1">Ungu</option>
                                <option value="#20c997">Toska</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-kaskita px-4">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function editCat(cat) {
    document.getElementById('editCatForm').action = "{{ url('categories') }}/" + cat.id;
    document.getElementById('editCatType').value = cat.type;
    document.getElementById('editCatName').value = cat.name;
    document.getElementById('editCatIcon').value = cat.icon;
    document.getElementById('editCatColor').value = cat.color;

    var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}
</script>
@endsection