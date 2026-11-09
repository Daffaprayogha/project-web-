<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') – KasKita UMKM</title>
    <!-- Favicon / Web Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.svg') }}">

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #198754;
            --primary-dark: #146c43;
            --primary-light: #20c997;
            --primary-subtle: #e8f5e9;
            --income: #198754;
            --expense: #dc3545;
            --info: #0d6efd;
            --dark: #1e293b;
            --gray-bg: #f8fafc;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--gray-bg);
            color: #334155;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.35rem;
            transition: transform 0.2s ease;
        }
        .sidebar-brand:hover {
            transform: scale(1.02);
            color: var(--primary-dark);
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            flex-grow: 1;
        }

        .nav-sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 12px;
            margin-bottom: 4px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .nav-sidebar-link:hover {
            color: var(--primary);
            background-color: var(--primary-subtle);
            transform: translateX(4px);
        }

        .nav-sidebar-link.active {
            color: #ffffff;
            background-color: var(--primary);
            font-weight: 600;
            box-shadow: 0 6px 14px rgba(25, 135, 84, 0.25);
            transform: translateX(4px);
        }

        .nav-sidebar-link i {
            font-size: 1.2rem;
            transition: transform 0.2s ease;
        }
        .nav-sidebar-link:hover i {
            transform: scale(1.15);
        }

        /* Main Content Wrapper */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 999;
            animation: fadeIn 0.5s ease both;
        }

        /* Cards */
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .card-custom:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        /* Staggered Entrance Animations */
        .animate-fade-in-up {
            animation: cardFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }
        .delay-5 { animation-delay: 0.40s; }

        @keyframes cardFadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .btn-kaskita {
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-kaskita:hover {
            background-color: var(--primary-dark);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(25, 135, 84, 0.25);
            transform: translateY(-2px);
        }

        .badge-income {
            background-color: #d1e7dd;
            color: #0f5132;
            font-weight: 600;
        }
        .badge-expense {
            background-color: #f8d7da;
            color: #842029;
            font-weight: 600;
        }

        /* Responsive Mobile Sidebar */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show {
                margin-left: 0;
            }
            #content-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar (Section F) -->
    <aside id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('img/logo.svg') }}" alt="KasKita Logo" style="width: 40px; height: 40px; object-fit: contain;" class="rounded-3 shadow-sm">
            <span>KasKita</span>
        </a>

        <!-- Business Tag -->
        <div class="px-3 py-2 border-bottom bg-light">
            <small class="text-muted d-block" style="font-size: 11px;">USAHA ANDA:</small>
            <span class="fw-bold text-dark text-truncate d-block small" title="{{ Auth::user()->business_name }}">
                {{ Auth::user()->business_name ?? 'Usaha UMKM' }}
            </span>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="nav-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('profile.index') }}" class="nav-sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profil</span>
            </a>
            <a href="{{ route('transactions.index') }}" class="nav-sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Transaksi</span>
            </a>
            <a href="{{ route('categories.index') }}" class="nav-sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i>
                <span>Kategori</span>
            </a>
            <a href="{{ route('reports.index') }}" class="nav-sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                <span>Laporan</span>
            </a>
            <a href="{{ route('notifications.index') }}" class="nav-sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                <span>Notifikasi</span>
                @php
                    $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 10px;">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('settings.index') }}" class="nav-sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
        </nav>

        <!-- Logout Button (Section F) -->
        <div class="p-3 border-top mt-auto">
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="border-radius: 12px;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="fw-semibold">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div id="content-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none border" id="toggleSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-sm-block">
                    <h5 class="fw-bold mb-0 text-dark">@yield('page_title', 'Dashboard Keuangan')</h5>
                    <small class="text-secondary">{{ Auth::user()->business_name }} &bull; {{ date('d F Y') }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Quick Add Transaction Button -->
                <button class="btn btn-kaskita btn-sm px-3 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#globalTransactionModal">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span class="d-none d-md-inline">Catat Transaksi</span>
                </button>

                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <a href="{{ route('notifications.index') }}" class="btn btn-light rounded-circle position-relative border" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-bell text-secondary"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        @endif
                    </a>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2 border px-2 py-1 rounded-pill" data-bs-toggle="dropdown">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar">
                        @else
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="fw-semibold small d-none d-md-inline pe-1">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down small text-secondary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 mt-2">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <strong class="d-block text-dark">{{ Auth::user()->name }}</strong>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i> Profil Usaha</a></li>
                        <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i> Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger py-2" href="javascript:void(0)" onclick="document.getElementById('logoutForm').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Body -->
        <main class="p-3 p-md-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm animate-fade-in-up" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm animate-fade-in-up" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-secondary small">
            <span>&copy; {{ date('Y') }} KasKita – Sistem Pencatatan Kas dan Keuangan UMKM.</span>
        </footer>
    </div>

    <!-- GLOBAL TRANSACTION MODAL -->
    <div class="modal fade" id="globalTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="txModalTitle">Catat Transaksi Kas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Jenis Transaksi <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="type" id="typeIncome" value="pemasukan" checked onchange="filterCategories('pemasukan')">
                                    <label class="btn btn-outline-success w-100 py-2 fw-semibold" for="typeIncome">
                                        <i class="bi bi-arrow-down-left"></i> Pemasukan
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="type" id="typeExpense" value="pengeluaran" onchange="filterCategories('pengeluaran')">
                                    <label class="btn btn-outline-danger w-100 py-2 fw-semibold" for="typeExpense">
                                        <i class="bi bi-arrow-up-right"></i> Pengeluaran
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Nominal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                <input type="number" name="amount" class="form-control form-control-lg fw-bold" placeholder="0" min="1" required id="globalAmountInput" oninput="updateRupiahPreview(this, 'globalAmountPreview')">
                            </div>
                            <div id="globalAmountPreview" class="small fw-semibold text-success mt-1"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Kategori</label>
                            <select name="category_id" class="form-select" id="globalCategorySelect">
                                <option value="">-- Pilih Kategori --</option>
                                @php
                                    $allCats = Auth::user()->categories()->orderBy('name')->get();
                                @endphp
                                @foreach($allCats as $cat)
                                    <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Status Pembayaran <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="status" id="globalStatusPaid" value="lunas" checked onchange="toggleDueDateContainer('global', false)">
                                    <label class="btn btn-outline-success w-100 py-2 fw-semibold" for="globalStatusPaid">
                                        <i class="bi bi-check-circle me-1"></i> Lunas
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="status" id="globalStatusUnpaid" value="belum_lunas" onchange="toggleDueDateContainer('global', true)">
                                    <label class="btn btn-outline-warning text-dark w-100 py-2 fw-semibold" for="globalStatusUnpaid">
                                        <i class="bi bi-clock-history me-1"></i> Belum Dibayar
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="globalDueDateContainer">
                            <label class="form-label fw-semibold small text-danger">
                                <i class="bi bi-calendar-event me-1"></i> Tanggal Bayar / Jatuh Tempo <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="due_date" id="globalDueDateInput" class="form-control border-warning bg-light">
                            <small class="text-muted" style="font-size: 11px;">Pengingat batas bayar otomatis masuk ke menu Notifikasi & dashboard.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Keterangan Transaksi <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" placeholder="Contoh: Gaji karyawan paruh waktu / Tagihan listrik / Kulakan beras" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan bon, nama langganan, atau nomor nota..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Foto Bukti / Struk / Nota (Opsional)</label>
                            <input type="file" name="receipt_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted" style="font-size: 11px;">Maks. 3MB (Format: JPG, PNG, WEBP)</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-kaskita px-4">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar on mobile
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Filter category dropdown based on type
        function filterCategories(type) {
            const select = document.getElementById('globalCategorySelect');
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
            // Reset to first visible
            select.value = '';
        }

        // Initialize category filter
        filterCategories('pemasukan');

        // Toggle Due Date input visibility
        function toggleDueDateContainer(prefix, isUnpaid) {
            const container = document.getElementById(prefix + 'DueDateContainer');
            const input = document.getElementById(prefix + 'DueDateInput');
            if (!container) return;
            if (isUnpaid) {
                container.classList.remove('d-none');
                if (input && !input.value) {
                    const d = new Date();
                    d.setDate(d.getDate() + 3);
                    input.value = d.toISOString().split('T')[0];
                }
            } else {
                container.classList.add('d-none');
            }
        }

        // Helper: Format number to Rupiah string
        function updateRupiahPreview(input, targetId) {
            const previewEl = document.getElementById(targetId);
            if (!previewEl) return;
            const val = parseFloat(input.value);
            if (!val || val <= 0) {
                previewEl.textContent = '';
                return;
            }
            previewEl.textContent = 'Rp ' + val.toLocaleString('id-ID');
        }

        // Global shortcut: Ctrl+K or Alt+N to open Catat Transaksi modal
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && e.key.toLowerCase() === 'k') || (e.altKey && e.key.toLowerCase() === 'n')) {
                e.preventDefault();
                const modalEl = document.getElementById('globalTransactionModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    setTimeout(() => document.getElementById('globalAmountInput')?.focus(), 400);
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>