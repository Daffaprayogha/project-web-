# -*- coding: utf-8 -*-
"""
Script to generate a highly detailed, professional Word (.docx) documentation
specifically focusing on:
1. FITUR-FITUR LENGKAP KASKITA
2. APA SAJA YANG DIPAKAI (TEKNOLOGI, TOOLS, PACKAGES & DEPENDENCIES)
3. DESAIN ANTARMUKA (UI/UX, DESIGN SYSTEM, PALET WARNA, KOMPONEN, & INTERAKSI)
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_padding(cell, top=120, bottom=120, left=160, right=160):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}>'
                      f'<w:top w:w="{top}" w:type="dxa"/>'
                      f'<w:bottom w:w="{bottom}" w:type="dxa"/>'
                      f'<w:left w:w="{left}" w:type="dxa"/>'
                      f'<w:right w:w="{right}" w:type="dxa"/>'
                      f'</w:tcMar>')
    tcPr.append(tcMar)

def set_cell_borders(cell, top=None, bottom=None, left=None, right=None):
    tcPr = cell._tc.get_or_add_tcPr()
    tcBorders = parse_xml(f'<w:tcBorders {nsdecls("w")}/>')
    
    borders = {'top': top, 'bottom': bottom, 'left': left, 'right': right}
    for border_name, border_style in borders.items():
        if border_style:
            val, sz, space, color = border_style
            b_el = parse_xml(f'<w:{border_name} {nsdecls("w")} w:val="{val}" w:sz="{sz}" w:space="{space}" w:color="{color}"/>')
            tcBorders.append(b_el)
        else:
            b_el = parse_xml(f'<w:{border_name} {nsdecls("w")} w:val="none"/>')
            tcBorders.append(b_el)
    tcPr.append(tcBorders)

def add_callout(doc, text, title="INFORMASI SISTEM", border_color="198754", bg_color="E8F5E9"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    set_cell_background(cell, bg_color)
    set_cell_padding(cell, top=140, bottom=140, left=200, right=180)
    set_cell_borders(cell, 
                     left=('single', '36', '0', border_color),
                     top=None, bottom=None, right=None)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(4)
    run_title = p.add_run(f"📌 {title}\n")
    run_title.bold = True
    run_title.font.name = 'Segoe UI'
    run_title.font.size = Pt(10.5)
    run_title.font.color.rgb = RGBColor(20, 108, 67)
    
    run_text = p.add_run(text)
    run_text.font.name = 'Segoe UI'
    run_text.font.size = Pt(10)
    run_text.font.color.rgb = RGBColor(51, 65, 85)
    
    p_after = doc.add_paragraph()
    p_after.paragraph_format.space_before = Pt(4)
    p_after.paragraph_format.space_after = Pt(6)

def format_table(table, col_widths, headers, data, header_bg="198754", zebra=True):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    # Header row
    hdr_cells = table.rows[0].cells
    for i, header_text in enumerate(headers):
        hdr_cells[i].text = header_text
        hdr_cells[i].width = col_widths[i]
        set_cell_background(hdr_cells[i], header_bg)
        set_cell_padding(hdr_cells[i], top=140, bottom=140, left=140, right=140)
        set_cell_borders(hdr_cells[i], 
                         top=('single', '6', '0', 'CCCCCC'),
                         bottom=('single', '18', '0', '146C43'),
                         left=('single', '4', '0', 'E2E8F0'),
                         right=('single', '4', '0', 'E2E8F0'))
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        for run in p.runs:
            run.font.bold = True
            run.font.name = 'Segoe UI'
            run.font.size = Pt(9.5)
            run.font.color.rgb = RGBColor(255, 255, 255)
            
    # Data rows
    for row_idx, row_data in enumerate(data):
        row_cells = table.add_row().cells
        bg_col = "F8FAFC" if (zebra and row_idx % 2 == 1) else "FFFFFF"
        for col_idx, cell_value in enumerate(row_data):
            row_cells[col_idx].text = str(cell_value)
            row_cells[col_idx].width = col_widths[col_idx]
            set_cell_background(row_cells[col_idx], bg_col)
            set_cell_padding(row_cells[col_idx], top=100, bottom=100, left=140, right=140)
            set_cell_borders(row_cells[col_idx], 
                             top=('single', '4', '0', 'E2E8F0'),
                             bottom=('single', '4', '0', 'E2E8F0'),
                             left=('single', '4', '0', 'E2E8F0'),
                             right=('single', '4', '0', 'E2E8F0'))
            p = row_cells[col_idx].paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for run in p.runs:
                run.font.name = 'Segoe UI'
                run.font.size = Pt(9)
                run.font.color.rgb = RGBColor(51, 65, 85)

def build_detailed_documentation():
    doc = docx.Document()
    
    # Page setup
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)
        section.page_width = Inches(8.5)
        section.page_height = Inches(11.0)
        
        footer = section.footer
        f_p = footer.paragraphs[0]
        f_p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        f_run = f_p.add_run("KasKita – Penjelasan Fitur, Teknologi (Tech Stack), dan Desain UI/UX")
        f_run.font.name = 'Segoe UI'
        f_run.font.size = Pt(8.5)
        f_run.font.color.rgb = RGBColor(148, 163, 184)

    # -------------------------------------------------------------
    # COVER / HEADER
    # -------------------------------------------------------------
    p_top = doc.add_paragraph()
    p_top.paragraph_format.space_before = Pt(30)
    
    p_tag = doc.add_paragraph()
    r_tag = p_tag.add_run("DOKUMENTASI MENDALAM: FITUR, TEKNOLOGI & DESAIN UI")
    r_tag.font.name = 'Segoe UI'
    r_tag.font.size = Pt(10.5)
    r_tag.bold = True
    r_tag.font.color.rgb = RGBColor(25, 135, 84)
    p_tag.paragraph_format.space_after = Pt(8)
    
    p_title = doc.add_paragraph()
    r_title = p_title.add_run("Sistem KasKita UMKM")
    r_title.font.name = 'Segoe UI'
    r_title.font.size = Pt(26)
    r_title.bold = True
    r_title.font.color.rgb = RGBColor(20, 108, 67)
    p_title.paragraph_format.space_after = Pt(4)
    
    p_sub = doc.add_paragraph()
    r_sub = p_sub.add_run("Bedah Lengkap Seluruh Fitur, Alat/Teknologi yang Digunakan (Tech Stack), serta Arsitektur Antarmuka Pengguna (UI/UX Design System)")
    r_sub.font.name = 'Segoe UI'
    r_sub.font.size = Pt(12)
    r_sub.font.color.rgb = RGBColor(71, 85, 105)
    p_sub.paragraph_format.space_after = Pt(20)
    
    # Divider line
    div_table = doc.add_table(rows=1, cols=1)
    div_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    c = div_table.cell(0, 0)
    c.width = Inches(6.5)
    set_cell_background(c, "198754")
    set_cell_padding(c, top=16, bottom=16, left=0, right=0)
    set_cell_borders(c, None, None, None, None)
    
    # Metadata Overview
    doc.add_paragraph().paragraph_format.space_after = Pt(14)
    meta_table = doc.add_table(rows=4, cols=2)
    meta_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_widths = [Inches(2.2), Inches(4.3)]
    meta_rows = [
        ("Nama Proyek Web", "KasKita – Sistem Manajemen Kas & Keuangan UMKM"),
        ("Fokus Dokumen Ini", "1. Fitur Fungsional, 2. Alat & Teknologi, 3. Antarmuka UI/UX"),
        ("Arsitektur", "Monolith MVC (Laravel) + Responsive Blade Components"),
        ("Tanggal Penyusunan", "Oktober 2026 | Versi 1.0 (Lengkap & Terverifikasi)")
    ]
    for idx, (label, val) in enumerate(meta_rows):
        cells = meta_table.rows[idx].cells
        cells[0].width = meta_widths[0]
        cells[1].width = meta_widths[1]
        cells[0].text = label
        cells[1].text = val
        set_cell_background(cells[0], "E8F5E9")
        set_cell_background(cells[1], "F8FAFC")
        set_cell_padding(cells[0], top=70, bottom=70, left=100, right=100)
        set_cell_padding(cells[1], top=70, bottom=70, left=100, right=100)
        set_cell_borders(cells[0], 
                         top=('single', '4', '0', 'CBD5E1'), bottom=('single', '4', '0', 'CBD5E1'),
                         left=('single', '4', '0', 'CBD5E1'), right=('single', '4', '0', 'CBD5E1'))
        set_cell_borders(cells[1], 
                         top=('single', '4', '0', 'CBD5E1'), bottom=('single', '4', '0', 'CBD5E1'),
                         left=('single', '4', '0', 'CBD5E1'), right=('single', '4', '0', 'CBD5E1'))
        p0 = cells[0].paragraphs[0]
        p0.runs[0].font.name = 'Segoe UI'
        p0.runs[0].font.size = Pt(9.5)
        p0.runs[0].font.bold = True
        p0.runs[0].font.color.rgb = RGBColor(20, 108, 67)
        p1 = cells[1].paragraphs[0]
        p1.runs[0].font.name = 'Segoe UI'
        p1.runs[0].font.size = Pt(9.5)
        p1.runs[0].font.color.rgb = RGBColor(30, 41, 59)

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAGIAN 1: APA SAJA YANG DIPAKAI (TEKNOLOGI, TOOLS, PACKAGES)
    # -------------------------------------------------------------
    h_sec1 = doc.add_heading(level=1)
    r = h_sec1.add_run("BAGIAN 1: Apa Saja yang Dipakai (Teknologi & Tools)")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Aplikasi KasKita dikembangkan dengan memanfaatkan kombinasi teknologi web modern yang tangguh, "
        "stabil, cepat, dan teruji di industri. Berikut adalah inventarisasi lengkap seluruh teknologi, dependensi, "
        "library, dan perkakas (tools) yang digunakan dalam pembangunan sistem:"
    )

    t_w = [Inches(1.5), Inches(1.8), Inches(3.2)]
    t_headers = ["Kategori", "Perangkat / Library", "Fungsi & Peranan Utama dalam KasKita"]
    t_data = [
        [
            "Backend Framework",
            "PHP 8.2 + Laravel Framework 10",
            "Fondasi utama logika server-side, routing RESTful, middleware auth & guest, Eloquent ORM untuk manipulasi database, CSRF protection, dan session management."
        ],
        [
            "Basis Data (Database)",
            "MySQL / MariaDB",
            "Sistem manajemen basis data relasional (RDBMS). Menyimpan entitas users, categories, transactions, dan notifications lengkap dengan index dan foreign keys."
        ],
        [
            "Database Migrations",
            "Laravel Schema Builder",
            "Versioning skema tabel database otomatis (tanpa perlu manual SQL) yang menjamin konsistensi struktur antar-komputer pengembang dan server hosting."
        ],
        [
            "Template Engine",
            "Laravel Blade",
            "Merender antarmuka HTML secara modular menggunakan pewarisan layout (@extends, @section, @yield) dengan proteksi sanitasi XSS bawaan ({{ }})."
        ],
        [
            "CSS Framework",
            "Bootstrap 5.3.2 (via CDN)",
            "Menyediakan sistem grid flexbox responsif 12 kolom, utility classes, komponen modal popup, dropdown, badges, buttons, cards, dan navigasi offcanvas mobile."
        ],
        [
            "Iconography",
            "Bootstrap Icons (v1.11.3)",
            "Koleksi lebih dari 2.000 icon vektor tajam untuk visualisasi jenis transaksi (panah masuk/keluar), dompet saldo kas, kategori belanja, lonceng notifikasi, dll."
        ],
        [
            "Visualisasi Grafik",
            "Chart.js (v4.x)",
            "Library JavaScript visualisasi data. Digunakan untuk membuat grafik garis multi-periode (harian, bulanan, tahunan) serta doughnut chart komposisi pengeluaran."
        ],
        [
            "Tipografi (Font)",
            "Google Fonts 'Poppins'",
            "Keluarga font sans-serif geometris modern dengan bobot 300, 400, 500, 600, 700, dan 800 yang memberikan kesan aplikasi keuangan profesional dan ramah."
        ],
        [
            "Web Server Lokal",
            "Apache XAMPP / PHP Artisan",
            "Lingkungan eksekusi web server lokal untuk melayani HTTP requests, file assets statis (CSS/JS/gambar), dan koneksi port MySQL."
        ],
        [
            "Export Engine",
            "Native PHP CSV Stream + BOM",
            "Pembangkit berkas spreadsheet Excel (.CSV) dengan UTF-8 Byte Order Mark (BOM) agar simbol mata uang Rupiah dan nama toko berkarakter khusus terbaca rapi di Microsoft Excel."
        ],
        [
            "Print Stylesheet Engine",
            "CSS @media print",
            "Engine styling khusus cetak yang menyembunyikan sidebar dan tombol aksi saat pengguna menekan tombol print, memformat lembar laporan menjadi standar kertas A4."
        ],
        [
            "Docx Automation Tool",
            "Python 3.12 + python-docx",
            "Script otomasi Python untuk membuat dan menata dokumen laporan teknis Microsoft Word (.docx) berformat formal secara instan."
        ]
    ]
    t_stack = doc.add_table(rows=1, cols=3)
    format_table(t_stack, t_w, t_headers, t_data)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(10)
    
    add_callout(
        doc,
        "Mengapa Kombinasi Ini Dipilih?\n"
        "• Ringan & Cepat: Tidak memerlukan proses build JavaScript berat (seperti Node.js node_modules raksasa di server hosting murah), sehingga dapat langsung dijalankan lancar di XAMPP.\n"
        "• Aman: Laravel memiliki proteksi built-in terhadap SQL Injection, XSS, dan CSRF tanpa perlu konfigurasi rumit.\n"
        "• Responsif: Bootstrap 5.3 memastikan tampilan tetap proporsional baik dibuka lewat ponsel Android pemilik toko maupun laptop kasir.",
        title="RASIONALISASI PEMILIHAN TEKNOLOGI"
    )

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAGIAN 2: RINCIAN FITUR-FITUR LENGKAP KASKITA
    # -------------------------------------------------------------
    h_sec2 = doc.add_heading(level=1)
    r = h_sec2.add_run("BAGIAN 2: Rincian Fitur-Fitur Lengkap KasKita")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Sistem KasKita dirancang dengan fokus pada kecepatan dan kemudahan pencatatan finansial bagi pelaku UMKM. "
        "Berikut adalah bedah fungsional seluruh fitur yang tersedia:"
    )

    features = [
        (
            "2.1 Fitur Landing Page & Edukasi Keuangan UMKM",
            "Tujuan: Memperkenalkan sistem kepada publik dan meningkatkan literasi finansial pengusaha kecil.\n"
            "Rincian Kemampuan:\n"
            "• Hero Banner Interaktif: Slogan persuasif dengan kartu simulasi dashboard toko kelontong live.\n"
            "• Segmen Solusi 4 Sektor Usaha: Penjelasan kegunaan KasKita khusus bagi Toko Sembako, Laundry, Warung Kuliner, dan Jasa Mikro.\n"
            "• Modul Edukasi Finansial (Modal Popup): 3 artikel edukasi praktis tentang penanganan piutang/bon pelanggan, kedisiplinan catatan harian, dan pemisahan uang usaha vs dompet pribadi yang dapat dibaca penuh tanpa reload halaman.\n"
            "• Counter Statistik Komunitas: Menampilkan metrik live pengguna terdaftar (120+), transaksi tercatat (500+), dan mitra UMKM aktif.\n"
            "• Testimoni Mitra: Ulasan nyata dari pedagang sembako, pengusaha laundry, dan kedai kopi."
        ),
        (
            "2.2 Fitur Otentikasi & Registrasi Usaha (Dengan Auto-Seeding Kategori)",
            "Tujuan: Pintu masuk aman dengan orientasi onboarding instan.\n"
            "Rincian Kemampuan:\n"
            "• Pendaftaran Lengkap: Menginput Nama Pemilik, Username Unik, Email, No. HP/WhatsApp, Nama Usaha, Jenis Usaha, dan Kata Sandi.\n"
            "• Otomasi Penanaman 10 Kategori Kas Bawaan: Begitu pemilik toko mendaftar, sistem otomatis membuatkan 10 kategori finansial standar UMKM ke akunnya:\n"
            "   - Pemasukan: Penjualan Produk, Pendapatan Jasa, Piutang Terbayar, Pemasukan Lainnya.\n"
            "   - Pengeluaran: Kulakan/Bahan Baku, Gaji Karyawan, Sewa Tempat, Listrik/Air/Internet, Operasional, Pengeluaran Lainnya.\n"
            "• Login Dual-Identifier: Pemilik usaha dapat masuk menggunakan Username atau Alamat Email.\n"
            "• Notifikasi Sambutan Otomatis: Sistem otomatis menyisipkan pesan ucapan selamat datang di lonceng notifikasi."
        ),
        (
            "2.3 Fitur Dashboard Analitik Finansial Interaktif",
            "Tujuan: Memberikan visualisasi kesehatan arus kas usaha secara instan dalam 3 detik pertama.\n"
            "Rincian Kemampuan:\n"
            "• 4 Kartu Metrik Utama: Total Saldo Kas Fisik Usaha, Pemasukan Bulan Ini, Pengeluaran Bulan Ini, dan Total Transaksi Tercatat.\n"
            "• Live Number Counter Animation: Efek animasi angka yang menghitung maju dari Rp 0 hingga nominal target saat dashboard dibuka.\n"
            "• Growth Comparison Rate (%): Menghitung otomatis persentase kenaikan/penurunan omzet dan belanja dibandingkan bulan sebelumnya.\n"
            "• Grafik Multi-Periode Dinamis (3 Tab): Pilihan switch periode instan:\n"
            "   1. Tab Per Hari: Memantau naik-turun arus kas harian 30 hari ke belakang.\n"
            "   2. Tab Per Bulan: Membandingkan performa 12 bulan terakhir dalam 1 tahun buku.\n"
            "   3. Tab Per Tahun: Tren pertumbuhan omzet usaha selama 5 tahun terakhir.\n"
            "• Live Touch/Hover Inspector Banner: Kotak ringkasan cerdas yang secara real-time menampilkan rincian Masuk, Keluar, dan Surplus saat kursor mouse digerakkan di atas chart.\n"
            "• Donut Chart Pengeluaran per Pos: Memetakan pos belanja terbesar bulan ini dengan fitur klik 'Interactive Pills' untuk menyorot potongan kategori tertentu.\n"
            "• Tombol Akselerator Cepat (+ Pemasukan / - Pengeluaran): Membuka formulir pencatatan kas modal langsung dari dashboard."
        ),
        (
            "2.4 Fitur Buku Kas Transaksi (Kas Masuk & Keluar)",
            "Tujuan: Pencatatan mutasi kas harian dengan kemampuan pelacakan data presisi.\n"
            "Rincian Kemampuan:\n"
            "• Entri Transaksi Terstruktur: Memilih jenis (Masuk/Keluar), kategori, nominal rupiah, tanggal transaksi, deskripsi ringkas, dan catatan nota.\n"
            "• Filter Multi-Parameter: Pengguna dapat menyaring data berdasarkan jenis kas, kategori spesifik, tanggal mulai, tanggal akhir, serta fitur pencarian teks bebas (keyword search) pada keterangan.\n"
            "• Rekapitulasi Real-Time Hasil Filter: 3 kotak ringkasan yang langsung menghitung total uang masuk, uang keluar, dan laba bersih khusus untuk data yang sedang tersaring.\n"
            "• CRUD Penuh: Fasilitas Edit Transaksi (memperbarui nominal/tanggal) dan Hapus Transaksi dengan modal dialog konfirmasi.\n"
            "• Paginasi Bersih: Pembagian halaman tabel (12 transaksi per lembar) dengan tetap mempertahankan filter yang aktif."
        ),
        (
            "2.5 Fitur Kustomisasi Pos Kategori",
            "Tujuan: Memberikan fleksibilitas bagi pemilik usaha mengelompokkan pos belanja unik tokonya.\n"
            "Rincian Kemampuan:\n"
            "• Pemisahan Tab: Tab Kategori Pemasukan dan Tab Kategori Pengeluaran.\n"
            "• Personalisasi Visual: Memilih nama pos, icon grafis (Bootstrap Icons), dan kode warna heksadesimal.\n"
            "• Ringkasan Finansial Tiap Pos: Menampilkan total jumlah transaksi yang pernah terjadi serta total perputaran rupiah pada pos tersebut."
        ),
        (
            "2.6 Fitur Laporan Keuangan, Ekspor Excel, dan Cetak Nota",
            "Tujuan: Rekapitulasi pembukuan resmi untuk evaluasi internal dan syarat perbankan (pengajuan KUR).\n"
            "Rincian Kemampuan:\n"
            "• Filter Periode Fleksibel: Pilihan laporan bulanan (Bulan & Tahun) atau rentang tanggal bebas (Custom Date Range).\n"
            "• Rekapitulasi Surplus/Defisit: Menampilkan posisi kas laba/rugi periode terpilih.\n"
            "• Ekspor Spreadsheet Excel (.CSV): Mengunduh file tabel lengkap dengan UTF-8 BOM, kop surat usaha, nama pemilik, nomor urut, kategori, dan baris total saldo akhir.\n"
            "• Cetak Nota / Simpan PDF (Print Layout): Lembar cetak formal bertata letak khusus yang menghilangkan elemen web (sidebar/topbar) dan menyajikan tabel formal disertai kolom tanda tangan pemilik usaha."
        ),
        (
            "2.7 Fitur Profil Usaha, Keamanan Akun, dan Pusat Notifikasi",
            "Tujuan: Manajemen data bisnis dan integritas keamanan akses.\n"
            "Rincian Kemampuan:\n"
            "• Pengaturan Data UMKM: Mengubah identitas nama usaha, bidang bisnis, dan nomor telepon kontak.\n"
            "• Ubah Kata Sandi Aman: Validasi verifikasi password lama sebelum mengizinkan pembuatan password baru 8 karakter.\n"
            "• Pusat Notifikasi: Menampilkan log pemberitahuan sistem, penandaan status 'Tandai Dibaca', dan 'Tandai Semua Dibaca'."
        )
    ]

    for title, desc in features:
        doc.add_heading(title, level=2)
        p = doc.add_paragraph()
        p.paragraph_format.line_spacing = 1.15
        p.paragraph_format.space_after = Pt(6)
        p.add_run(desc)

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAGIAN 3: DESAIN ANTARMUKA (UI / UX DESIGN SYSTEM)
    # -------------------------------------------------------------
    h_sec3 = doc.add_heading(level=1)
    r = h_sec3.add_run("BAGIAN 3: Desain Antarmuka (UI/UX Design System)")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Antarmuka pengguna (UI) KasKita dirancang dengan standar visual modern kelas dunia, menjauhi tampilan lawas "
        "yang kaku dan membosankan. Desain mengadopsi filosofi 'Fresh & Trustworthy Financial System' dengan "
        "aksen warna hijau zamrud (Emerald Green) yang melambangkan pertumbuhan uang kas dan kemakmuran usaha."
    )

    doc.add_heading("3.1 Sistem Warna (Design Tokens & Color Palette)", level=2)
    
    color_w = [Inches(1.5), Inches(1.2), Inches(1.3), Inches(2.5)]
    color_headers = ["Peran Token", "Kode Hex", "Nama Visual", "Tujuan & Penggunaan dalam UI"]
    color_data = [
        ["Primary Main", "#198754", "Emerald Green", "Warna identitas utama brand KasKita, tombol aksi utama, navbar landing, dan badge aktif."],
        ["Primary Dark", "#146c43", "Deep Forest", "Efek hover pada tombol utama, footer landing page, dan header tabel laporan."],
        ["Primary Light", "#20c997", "Mint Teal", "Garis tren aksen, indikator pemasukan sekunder, dan border card aktif."],
        ["Primary Subtle", "#e8f5e9", "Soft Green Tint", "Latar belakang badge status, card info selamat datang, dan highlight menu."],
        ["Expense / Kas Keluar", "#dc3545", "Crimson Red", "Nominal pengeluaran (-Rp), tombol hapus, badge kas keluar, dan chart donat belanja."],
        ["Net Surplus / Info", "#0d6efd", "Royal Blue", "Nilai selisih kas bersih positif, icon transaksi receipt, dan badge kategori jasa."],
        ["Background Body", "#f8fafc", "Cool Gray 50", "Warna dasar latar belakang dashboard dan aplikasi agar nyaman dipandang lama di mata."],
        ["Card & Surface", "#ffffff", "Pure White", "Permukaan kartu statistik, tabel transaksi, sidebar, dan modal dialog popup."],
        ["Text Primary", "#334155", "Slate 700", "Warna teks judul dan paragraf utama untuk rasio kontras keterbacaan tinggi."],
        ["Text Muted", "#64748b", "Slate 500", "Keterangan label kecil, tanggal transaksi, catatan pelengkap, dan placeholder."]
    ]
    t_color = doc.add_table(rows=1, cols=4)
    format_table(t_color, color_w, color_headers, color_data)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(10)

    doc.add_heading("3.2 Tipografi & Hierarki Visual (Typography)", level=2)
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(6)
    p.add_run(
        "Aplikasi menggunakan Google Fonts 'Poppins' di seluruh halaman, dengan penataan hierarki bobot yang terstruktur:\n"
        "• Display Heading (Hero Banner): Poppins 800 (ExtraBold) & 700 (Bold) ukuran 2.5rem - 3.0rem.\n"
        "• Section Title & Card Title: Poppins 700 (Bold) ukuran 1.25rem - 1.75rem.\n"
        "• Metric Digits (Angka Rupiah): Poppins 700 (Bold) ukuran 1.4rem - 1.8rem untuk penekanan angka kas.\n"
        "• Body & Table Text: Poppins 400 (Regular) dan 500 (Medium) ukuran 0.9rem - 1.0rem.\n"
        "• Small Label & Badge: Poppins 600 (SemiBold) ukuran 0.75rem - 0.85rem."
    )

    doc.add_heading("3.3 Komponen Antarmuka Khas (Custom UI Components)", level=2)
    
    comp_w = [Inches(1.8), Inches(4.7)]
    comp_headers = ["Komponen UI", "Spesifikasi & Karakteristik Desain"]
    comp_data = [
        [
            "Sidebar Sticky / Fixed (260px)",
            "Menu navigasi tetap di sisi kiri layar dengan header logo KasKita berikon dompet, tag nama usaha UMKM pengguna, link menu dengan indikator active hijau, dan tombol logout aman."
        ],
        [
            "Responsive Mobile Offcanvas",
            "Pada layar smartphone (<992px), sidebar secara otomatis tersembunyi ke luar layar (margin-left: -260px) dan dapat dimunculkan secara mulus menggunakan tombol hamburger toggle di topbar."
        ],
        [
            "Statistic Metric Cards",
            "Kartu putih dengan efek bayangan halus (box-shadow 0 4px 12px rgba(0,0,0,0.04)), aksen garis tepi kiri berwarna (border-left 5px solid), icon berlatar warna lembut, dan teks rupiah besar."
        ],
        [
            "Live Touch Inspector Card",
            "Banner dinamis bergradien lembut (#f8fafc ke #edf7f2) di atas grafik yang menampilkan angka Pemasukan, Pengeluaran, dan Surplus secara seketika saat kursor digerakkan di atas grafik."
        ],
        [
            "Interactive Donut Category Pills",
            "Kapsul filter kategori di bawah diagram lingkaran donat. Memiliki efek hover terangkat (translateY(-2px)) dan dapat diklik untuk menyorot irisan kategori terkait."
        ],
        [
            "Global Quick Transaction Modal",
            "Jendela modal dialog yang dapat diakses dari halaman mana pun melalui topbar (+ Catat Kas) maupun dashboard, dengan validasi input instan dan tombol simpan hijau."
        ],
        [
            "Print Sheet Layout (@media print)",
            "Tata letak khusus saat cetak kertas A4. Menghilangkan elemen navigasi, menambahkan kop surat toko, garis pemisah formal, dan kolom tanda tangan basah pemilik toko."
        ]
    ]
    t_comp = doc.add_table(rows=1, cols=2)
    format_table(t_comp, comp_w, comp_headers, comp_data)

    doc.add_paragraph().paragraph_format.space_after = Pt(10)

    doc.add_heading("3.4 Mikro-Interaksi & Animasi Pengalaman Pengguna (UX Delighters)", level=2)
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(6)
    p.add_run(
        "Untuk menciptakan pengalaman pengguna yang memuaskan dan terasa hidup (alive), KasKita dilengkapi "
        "berbagai efek mikro-animasi CSS & JS terukur:\n"
        "• Staggered Entrance Animation: Saat halaman dashboard dibuka, kartu ucapan, 4 kartu statistik, grafik, dan tabel muncul bertahap secara halus (delay 0.08s s/d 0.50s) menggunakan kurva cubic-bezier(0.16, 1, 0.3, 1).\n"
        "• Live Number Counter: Angka saldo dan omzet kas tidak sekadar muncul diam, melainkan berhitung cepat dari angka 0 menuju angka riil dalam durasi 1 detik.\n"
        "• Button Elevation: Tombol KasKita memiliki transisi melayang ke atas (-2px) disertai bayangan lembut saat disentuh kursor kursor (hover).\n"
        "• Table Row Highlight: Setiap baris transaksi di tabel berganti latar belakang menjadi warna hijau pastel (#f1f8f4) saat kursor diarahkan, mencegah mata keliru membaca baris data nominal."
    )

    doc.add_paragraph().paragraph_format.space_after = Pt(16)
    
    p_end = doc.add_paragraph()
    p_end.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_end = p_end.add_run("--- Dokumen Selesai: Fitur, Teknologi, & Desain UI KasKita UMKM ---")
    r_end.font.name = 'Segoe UI'
    r_end.font.size = Pt(10)
    r_end.font.italic = True
    r_end.font.color.rgb = RGBColor(148, 163, 184)

    output_filename = "Dokumentasi_Fitur_TechStack_UI_KasKita.docx"
    doc.save(output_filename)
    print(f"Detailed Document successfully created: {os.path.abspath(output_filename)}")

if __name__ == "__main__":
    build_detailed_documentation()
