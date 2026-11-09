# -*- coding: utf-8 -*-
"""
Script to generate a comprehensive, professionally styled Word (.docx) documentation
for the KasKita UMKM Finance System web application.
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import qn, nsdecls

def create_element(name):
    return OxmlElement(name)

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

def add_callout(doc, text, title="CATATAN PENTING", border_color="198754", bg_color="E8F5E9"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    set_cell_background(cell, bg_color)
    set_cell_padding(cell, top=140, bottom=140, left=200, right=180)
    
    # Left thick border, no other borders
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

def build_documentation():
    doc = docx.Document()
    
    # Page setup - Margins (1 inch)
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)
        section.page_width = Inches(8.5)
        section.page_height = Inches(11.0)
        
        # Header & Footer setup
        footer = section.footer
        f_p = footer.paragraphs[0]
        f_p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        f_run = f_p.add_run("KasKita – Sistem Manajemen Keuangan UMKM | Dokumentasi Sistem")
        f_run.font.name = 'Segoe UI'
        f_run.font.size = Pt(8.5)
        f_run.font.color.rgb = RGBColor(148, 163, 184)

    # -------------------------------------------------------------
    # COVER / TITLE BLOCK
    # -------------------------------------------------------------
    p_top_space = doc.add_paragraph()
    p_top_space.paragraph_format.space_before = Pt(36)
    
    # Badge / Category
    p_badge = doc.add_paragraph()
    run_badge = p_badge.add_run("DOKUMENTASI TEKNIS & PANDUAN PENGGUNA SISTEM")
    run_badge.font.name = 'Segoe UI'
    run_badge.font.size = Pt(10)
    run_badge.bold = True
    run_badge.font.color.rgb = RGBColor(25, 135, 84) # Primary Emerald
    p_badge.paragraph_format.space_after = Pt(8)
    
    # Title
    p_title = doc.add_paragraph()
    run_title = p_title.add_run("KasKita – Solusi Finansial UMKM")
    run_title.font.name = 'Segoe UI'
    run_title.font.size = Pt(26)
    run_title.bold = True
    run_title.font.color.rgb = RGBColor(20, 108, 67) # Darker Green
    p_title.paragraph_format.space_after = Pt(4)
    
    # Subtitle
    p_sub = doc.add_paragraph()
    run_sub = p_sub.add_run("Sistem Pencatatan Kas, Analisis Arus Kas, dan Laporan Keuangan Berbasis Web untuk Pelaku Usaha Mikro, Kecil, dan Menengah")
    run_sub.font.name = 'Segoe UI'
    run_sub.font.size = Pt(13)
    run_sub.font.color.rgb = RGBColor(71, 85, 105)
    p_sub.paragraph_format.space_after = Pt(24)
    
    # Decorative horizontal line table
    line_table = doc.add_table(rows=1, cols=1)
    line_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    line_cell = line_table.cell(0, 0)
    line_cell.width = Inches(6.5)
    set_cell_background(line_cell, "198754")
    set_cell_padding(line_cell, top=20, bottom=20, left=0, right=0)
    set_cell_borders(line_cell, None, None, None, None)
    
    # Metadata Table
    doc.add_paragraph().paragraph_format.space_after = Pt(16)
    meta_table = doc.add_table(rows=5, cols=2)
    meta_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_widths = [Inches(2.2), Inches(4.3)]
    meta_info = [
        ("Nama Aplikasi", "KasKita (UMKM Financial Management System)"),
        ("Teknologi Utama", "PHP 8.2 / Laravel Framework (MVC Architecture)"),
        ("Tampilan & Frontend", "Blade Engine, Bootstrap 5.3, Bootstrap Icons, Chart.js"),
        ("Basis Data (Database)", "MySQL / MariaDB (Relational Database)"),
        ("Dokumen Dibuat", "Oktober 2026 | Versi Rilis 1.0 (Production Ready)")
    ]
    for idx, (label, val) in enumerate(meta_info):
        row_c = meta_table.rows[idx].cells
        row_c[0].width = meta_widths[0]
        row_c[1].width = meta_widths[1]
        row_c[0].text = label
        row_c[1].text = val
        set_cell_background(row_c[0], "E8F5E9")
        set_cell_background(row_c[1], "F8FAFC")
        set_cell_padding(row_c[0], top=80, bottom=80, left=120, right=120)
        set_cell_padding(row_c[1], top=80, bottom=80, left=120, right=120)
        set_cell_borders(row_c[0], 
                         top=('single', '4', '0', 'CBD5E1'), 
                         bottom=('single', '4', '0', 'CBD5E1'),
                         left=('single', '4', '0', 'CBD5E1'),
                         right=('single', '4', '0', 'CBD5E1'))
        set_cell_borders(row_c[1], 
                         top=('single', '4', '0', 'CBD5E1'), 
                         bottom=('single', '4', '0', 'CBD5E1'),
                         left=('single', '4', '0', 'CBD5E1'),
                         right=('single', '4', '0', 'CBD5E1'))
        
        p0 = row_c[0].paragraphs[0]
        p0.runs[0].font.name = 'Segoe UI'
        p0.runs[0].font.size = Pt(9.5)
        p0.runs[0].font.bold = True
        p0.runs[0].font.color.rgb = RGBColor(20, 108, 67)
        
        p1 = row_c[1].paragraphs[0]
        p1.runs[0].font.name = 'Segoe UI'
        p1.runs[0].font.size = Pt(9.5)
        p1.runs[0].font.color.rgb = RGBColor(30, 41, 59)
    
    doc.add_page_break()

    # -------------------------------------------------------------
    # DAFTAR ISI (RINGKASAN STRUKTUR DOKUMEN)
    # -------------------------------------------------------------
    h_toc = doc.add_heading(level=1)
    r_toc = h_toc.add_run("Daftar Isi Dokumentasi")
    r_toc.font.name = 'Segoe UI'
    r_toc.font.size = Pt(16)
    r_toc.font.color.rgb = RGBColor(20, 108, 67)
    h_toc.paragraph_format.space_before = Pt(0)
    h_toc.paragraph_format.space_after = Pt(12)
    
    toc_items = [
        ("BAB 1: RINGKASAN EKSEKUTIF & LATAR BELAKANG", "Tinjauan umum, urgensi digitalisasi kas UMKM, dan visi KasKita"),
        ("BAB 2: TARGET PENGGUNA & KARAKTERISTIK BISNIS", "Toko Sembako/Kelontong, Usaha Laundry, Warung Makan/Kuliner, dan Sektor Jasa"),
        ("BAB 3: ARSITEKTUR TEKNOLOGI & INFRASTRUKTUR", "Spesifikasi backend Laravel, frontend Bootstrap 5 & Chart.js, dan pola MVC"),
        ("BAB 4: STRUKTUR DATA DAN SKEMA BASIS DATA", "Rincian tabel Users, Categories, Transactions, dan Notifications beserta relasinya"),
        ("BAB 5: RINCIAN MODUL DAN FITUR-FITUR UTAMA", "Landing Page Publik, Otentikasi & Auto-Seeding, Dashboard Analitik, Transaksi Kas, Manajemen Kategori, Laporan Keuangan & Ekspor, Notifikasi"),
        ("BAB 6: LOGIKA BISNIS & ALUR KERJA SISTEM (USER FLOW)", "Proses onboarding, pencatatan transaksi masuk/keluar, kalkulasi laba, dan rekonsiliasi"),
        ("BAB 7: FITUR KEAMANAN, VALIDASI, & INTEGRITAS DATA", "Enkripsi password, CSRF Token, Eloquent SQL Injection protection, dan validasi input"),
        ("BAB 8: PANDUAN INSTALASI & PENGOPERASIAN SERVER", "Persyaratan sistem, konfigurasi .env, migrasi database, dan server lokal XAMPP"),
        ("BAB 9: KESIMPULAN & RENCANA PENGEMBANGAN LANJUTAN", "Rangkuman nilai manfaat dan roadmap fitur mendatang (POS, Multi-user, WhatsApp)")
    ]
    
    for title, desc in toc_items:
        p_item = doc.add_paragraph()
        p_item.paragraph_format.space_before = Pt(3)
        p_item.paragraph_format.space_after = Pt(3)
        r_t = p_item.add_run(f"• {title}\n")
        r_t.font.name = 'Segoe UI'
        r_t.font.size = Pt(10)
        r_t.bold = True
        r_t.font.color.rgb = RGBColor(25, 135, 84)
        
        r_d = p_item.add_run(f"   {desc}")
        r_d.font.name = 'Segoe UI'
        r_d.font.size = Pt(9)
        r_d.font.color.rgb = RGBColor(100, 116, 139)

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAB 1: RINGKASAN EKSEKUTIF & LATAR BELAKANG
    # -------------------------------------------------------------
    h1 = doc.add_heading(level=1)
    r = h1.add_run("BAB 1: Ringkasan Eksekutif & Latar Belakang")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "KasKita adalah aplikasi web pencatatan keuangan dan manajemen arus kas (cash flow) yang dirancang secara spesifik "
        "untuk menjawab problematika finansial mendasar yang dihadapi oleh jutaan pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) "
        "di Indonesia. Dalam dinamika operasional sehari-hari, mayoritas pemilik usaha kecil masih mengandalkan pencatatan "
        "manual menggunakan buku tulis fisik atau bahkan tidak mencatat aliran dana sama sekali. Pola pembukuan konvensional "
        "tersebut memiliki berbagai risiko krusial, antara lain buku catatan rusak atau hilang, selisih perhitungan uang fisik "
        "dengan catatan, tercampurnya uang pribadi dengan uang modal dagang, serta ketidakmampuan pelaku usaha mengetahui "
        "secara pasti apakah bisnis mereka sesungguhnya mengalami keuntungan (laba) atau kerugian (defisit)."
    )
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Melalui KasKita, seluruh proses administrasi finansial diubah menjadi pengalaman digital yang intuitif, cepat, "
        "dan dapat diakses dari perangkat apa pun (komputer, laptop, tablet, maupun smartphone). KasKita tidak hanya berfungsi "
        "sebagai buku kas digital, melainkan juga sebagai asisten finansial yang menyajikan ringkasan saldo kas real-time, "
        "visualisasi tren pemasukan dan pengeluaran secara interaktif, kategorisasi pos belanja, serta kemampuan mengekspor "
        "laporan pembukuan resmi yang siap diserahkan kepada perbankan atau mitra bisnis."
    )
    
    add_callout(
        doc,
        "Visi Utama KasKita: Memberdayakan pelaku UMKM dengan alat bantu pencatatan finansial berstandar modern namun tanpa "
        "kompleksitas software akuntansi rumit (seperti debit/kredit ganda yang membingungkan bagi pemula). Cukup catat kas masuk, "
        "kas keluar, pilih kategori, dan biarkan sistem menyajikan analisa kesehatan keuangan secara otomatis.",
        title="VISI & FILOSOFI DESAIN KASKITA"
    )

    # -------------------------------------------------------------
    # BAB 2: TARGET PENGGUNA & KARAKTERISTIK BISNIS
    # -------------------------------------------------------------
    h2 = doc.add_heading(level=1)
    r = h2.add_run("BAB 2: Target Pengguna & Karakteristik Bisnis")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Aplikasi KasKita dikembangkan dengan mengadaptasi karakteristik riil operasional usaha mikro di lapangan. "
        "Berikut adalah 4 persona segmen usaha yang menjadi prioritas utama implementasi KasKita:"
    )
    
    col_w = [Inches(1.8), Inches(2.3), Inches(2.4)]
    persona_headers = ["Sektor Usaha", "Tantangan Finansial", "Solusi KasKita"]
    persona_data = [
        [
            "Toko Kelontong & Sembako",
            "Tingginya frekuensi kulakan grosir harian, bon/piutang pelanggan langganan yang sering tercecer, modal kulakan terpakai untuk dapur pribadi.",
            "Pencatatan kas masuk per shift penjualan, pos belanja stok kulakan terpisah, pemantauan saldo riil harian."
        ],
        [
            "Usaha Laundry Kiloan / Satuan",
            "Pembelian deterjen, pewangi, plastik, dan biaya listrik/air operasional tinggi yang sulit dihitung margin keuntungannya secara harian.",
            "Kategori otomatis untuk bahan kimia laundry, utilitas listrik/air, serta rekapitulasi laba bersih bulanan."
        ],
        [
            "Warung Kuliner & Kedai Kopi",
            "Belanja pasar subuh bahan baku segar mudah busuk, pemasukan tunai vs QRIS yang perlu direkonsiliasi di malam hari.",
            "Kemudahan entri transaksi dari smartphone saat di pasar atau di meja kasir, grafik pergerakan omzet mingguan."
        ],
        [
            "Jasa Mikro (Barbershop, Bengkel, Jahit)",
            "Pemasukan utama berasal dari jasa keahlian, pengeluaran untuk alat kerja dan upah bagi hasil atau gaji asisten.",
            "Kategori terdedikasi untuk Pendapatan Jasa dan Gaji Karyawan, pemantauan kas bersih akhir bulan."
        ]
    ]
    
    t_persona = doc.add_table(rows=1, cols=3)
    format_table(t_persona, col_w, persona_headers, persona_data)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # -------------------------------------------------------------
    # BAB 3: ARSITEKTUR TEKNOLOGI & INFRASTRUKTUR
    # -------------------------------------------------------------
    h3 = doc.add_heading(level=1)
    r = h3.add_run("BAB 3: Arsitektur Teknologi & Infrastruktur")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "KasKita dibangun di atas fondasi teknologi web industri modern yang mengutamakan kecepatan eksekusi, stabilitas, "
        "serta kemudahan pemeliharaan (maintainability). Arsitektur aplikasi mengadopsi model-view-controller (MVC) standar "
        "pada ekosistem Laravel:"
    )
    
    tech_w = [Inches(1.5), Inches(2.2), Inches(2.8)]
    tech_headers = ["Komponen", "Teknologi Terpilih", "Deskripsi & Peran Fungsional"]
    tech_data = [
        ["Backend Core", "PHP 8.2 + Laravel Framework", "Routing engine cepat, middleware autentikasi, Eloquent ORM, migrasi skema database terstruktur."],
        ["Database Engine", "MySQL / MariaDB", "Penyimpanan data relasional berkecepatan tinggi dengan indeks foreign key, integritas referensial CASCADE dan SET NULL."],
        ["Templating", "Laravel Blade Engine", "Pewarisan layout modular (layouts.app dan layouts.landing), sanitasi XSS bawaan {{ }}, dan injeksi komponen dinamis."],
        ["Frontend UI", "Bootstrap 5.3 + Custom CSS", "Sistem grid responsif kelas dunia, komponen modal interaktif, tema warna Emerald Green Kustom (#198754)."],
        ["Iconography", "Bootstrap Icons (v1.11.3)", "Ikon vektor modern untuk navigasi, indikator jenis transaksi, serta representasi visual kategori finansial."],
        ["Visual Analytics", "Chart.js Library", "Grafik garis interaktif (multi-dataset arus kas harian/bulanan/tahunan) dan doughnut chart komposisi pengeluaran."],
        ["Font Typography", "Google Fonts 'Poppins'", "Tipografi modern ramah keterbacaan dengan bobot 300, 400, 500, 600, 700, dan 800."],
        ["Export Output", "Native CSV Stream + Print Engine", "Ekspor data transaksi ramah Microsoft Excel dengan UTF-8 BOM dan antarmuka cetak nota/laporan siap printer."]
    ]
    
    t_tech = doc.add_table(rows=1, cols=3)
    format_table(t_tech, tech_w, tech_headers, tech_data)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(8)
    
    add_callout(
        doc,
        "Arsitektur Multi-Tenant Sederhana (User Scoped Data): Setiap pengguna yang terdaftar memiliki isolasi data transaksi "
        "dan kategori secara ketat melalui relasi 'user_id'. Tidak ada pengguna yang dapat melihat, mengubah, atau menghapus "
        "catatan keuangan milik UMKM lain, menjamin kerahasiaan omzet usaha secara mutlak.",
        title="ISOLASI KEAMANAN DATA PENGGUNA"
    )

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAB 4: STRUKTUR DATA DAN SKEMA BASIS DATA
    # -------------------------------------------------------------
    h4 = doc.add_heading(level=1)
    r = h4.add_run("BAB 4: Struktur Data dan Skema Basis Data")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Basis data KasKita dirancang secara efisien untuk mendukung pencatatan volume transaksi finansial yang besar "
        "dengan kecepatan agregasi tinggi (SUM, COUNT, GROUP BY). Terdapat 4 entitas tabel utama yang saling berelasi:"
    )
    
    # Sub-bab 4.1 Tabel Users
    doc.add_heading("4.1 Tabel 'users' (Data Pemilik & Entitas Usaha)", level=2)
    user_cols_w = [Inches(1.2), Inches(1.2), Inches(1.1), Inches(3.0)]
    user_headers = ["Nama Kolom", "Tipe Data", "Atribut / Null", "Keterangan & Penggunaan"]
    user_data = [
        ["id", "BIGINT UNSIGNED", "PK, Auto Inc", "ID unik pengenal pengguna sistem."],
        ["name", "VARCHAR(255)", "NOT NULL", "Nama lengkap pemilik usaha UMKM."],
        ["username", "VARCHAR(255)", "UNIQUE, NOT NULL", "Nama pengguna unik untuk proses login."],
        ["email", "VARCHAR(255)", "UNIQUE, NOT NULL", "Alamat surat elektronik untuk verifikasi & login."],
        ["phone", "VARCHAR(255)", "NULLABLE", "Nomor telepon / WhatsApp pemilik bisnis."],
        ["business_name", "VARCHAR(255)", "NULLABLE", "Nama brand atau badan usaha (misal: Toko Berkah)."],
        ["business_type", "VARCHAR(255)", "NULLABLE", "Kategori sektor (Kuliner, Kelontong, Laundry, Jasa)."],
        ["avatar", "VARCHAR(255)", "NULLABLE", "Jalur path foto profil / avatar pengguna."],
        ["password", "VARCHAR(255)", "NOT NULL", "Kata sandi akun terenkripsi (Bcrypt / Argon2id)."],
        ["is_active", "BOOLEAN", "DEFAULT TRUE", "Status keaktifan akun (proteksi penonaktifan akses)."],
        ["created_at", "TIMESTAMP", "NULLABLE", "Waktu pembuatan akun pertama kali."]
    ]
    t_user = doc.add_table(rows=1, cols=4)
    format_table(t_user, user_cols_w, user_headers, user_data)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # Sub-bab 4.2 Tabel Categories
    doc.add_heading("4.2 Tabel 'categories' (Klasifikasi Transaksi)", level=2)
    cat_cols_w = [Inches(1.2), Inches(1.2), Inches(1.1), Inches(3.0)]
    cat_headers = ["Nama Kolom", "Tipe Data", "Atribut / Null", "Keterangan & Penggunaan"]
    cat_data = [
        ["id", "BIGINT UNSIGNED", "PK, Auto Inc", "ID unik kategori."],
        ["user_id", "BIGINT UNSIGNED", "FK, NULLABLE", "Relasi ke tabel users. Cascade on delete."],
        ["name", "VARCHAR(255)", "NOT NULL", "Nama pos kategori (misal: Belanja Stok, Gaji)."],
        ["type", "ENUM", "NOT NULL", "Pilihan: 'pemasukan' atau 'pengeluaran'."],
        ["icon", "VARCHAR(255)", "DEFAULT 'bi-tag'", "Class icon Bootstrap Icons yang merepresentasikan pos."],
        ["color", "VARCHAR(255)", "DEFAULT '#198754'", "Kode warna Hexadecimal untuk aksen visual grafik."],
        ["created_at", "TIMESTAMP", "NULLABLE", "Waktu pembuatan kategori."]
    ]
    t_cat = doc.add_table(rows=1, cols=4)
    format_table(t_cat, cat_cols_w, cat_headers, cat_data)

    doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # Sub-bab 4.3 Tabel Transactions
    doc.add_heading("4.3 Tabel 'transactions' (Buku Kas & Transaksi Finansial)", level=2)
    tx_cols_w = [Inches(1.2), Inches(1.2), Inches(1.1), Inches(3.0)]
    tx_headers = ["Nama Kolom", "Tipe Data", "Atribut / Null", "Keterangan & Penggunaan"]
    tx_data = [
        ["id", "BIGINT UNSIGNED", "PK, Auto Inc", "ID unik transaksi kas."],
        ["user_id", "BIGINT UNSIGNED", "FK, NOT NULL", "Relasi ke pemilik transaksi (users.id). Cascade delete."],
        ["category_id", "BIGINT UNSIGNED", "FK, NULLABLE", "Relasi ke categories.id. Set Null saat kategori dihapus."],
        ["type", "ENUM", "NOT NULL", "Pilihan jenis kas: 'pemasukan' atau 'pengeluaran'."],
        ["amount", "DECIMAL(15, 2)", "NOT NULL", "Nominal transaksi dengan presisi hingga triliunan rupiah."],
        ["transaction_date", "DATE", "NOT NULL", "Tanggal terjadinya transaksi kas."],
        ["description", "VARCHAR(255)", "NOT NULL", "Uraian ringkas tujuan transaksi (misal: Penjualan 5 Karung Beras)."],
        ["notes", "TEXT", "NULLABLE", "Catatan pelengkap, rincian bon, atau informasi pihak ketiga."],
        ["receipt_image", "VARCHAR(255)", "NULLABLE", "Jalur berkas foto kuitansi atau nota fisik digital."],
        ["created_at", "TIMESTAMP", "NULLABLE", "Waktu pencatatan rekaman data ke dalam sistem."]
    ]
    t_tx = doc.add_table(rows=1, cols=4)
    format_table(t_tx, tx_cols_w, tx_headers, tx_data)

    doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # Sub-bab 4.4 Tabel Notifications
    doc.add_heading("4.4 Tabel 'notifications' (Pusat Pemberitahuan Pengguna)", level=2)
    notif_cols_w = [Inches(1.2), Inches(1.2), Inches(1.1), Inches(3.0)]
    notif_headers = ["Nama Kolom", "Tipe Data", "Atribut / Null", "Keterangan & Penggunaan"]
    notif_data = [
        ["id", "BIGINT UNSIGNED", "PK, Auto Inc", "ID unik notifikasi."],
        ["user_id", "BIGINT UNSIGNED", "FK, NOT NULL", "Relasi ke penerima pemberitahuan (users.id)."],
        ["title", "VARCHAR(255)", "NOT NULL", "Judul ringkas pemberitahuan sistem."],
        ["message", "TEXT", "NOT NULL", "Isi pesan informasi, pengingat, atau konfirmasi."],
        ["type", "VARCHAR(255)", "DEFAULT 'info'", "Tipe notifikasi: 'success', 'warning', 'info', 'danger'."],
        ["is_read", "BOOLEAN", "DEFAULT FALSE", "Status pembacaan pesan oleh pengguna."]
    ]
    t_notif = doc.add_table(rows=1, cols=4)
    format_table(t_notif, notif_cols_w, notif_headers, notif_data)

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAB 5: RINCIAN MODUL DAN FITUR-FITUR UTAMA
    # -------------------------------------------------------------
    h5 = doc.add_heading(level=1)
    r = h5.add_run("BAB 5: Rincian Modul dan Fitur-Fitur Utama")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Aplikasi KasKita dikelompokkan ke dalam beberapa modul fungsional terintegrasi yang saling mendukung "
        "alur kerja pencatatan dan evaluasi finansial usaha:"
    )

    modules = [
        (
            "5.1 Halaman Beranda Publik (Landing Page)",
            "Menjadi etalase utama perkenalan sistem KasKita kepada calon pengguna dan masyarakat umum. Fitur-fitur di dalamnya meliputi:\n"
            "• Hero Section Modern: Menampilkan slogan persuasif, tombol ajakan registrasi, serta visual mockup kartu dashboard real-time yang memuat saldo dan contoh transaksi toko.\n"
            "• Nilai Jual & Fitur Utama: Menjelaskan 6 fitur unggulan aplikasi (Pencatatan Pemasukan, Pencatatan Pengeluaran, Manajemen Kategori, Dashboard Visual, Laporan Keuangan, Ekspor Excel/PDF).\n"
            "• Sektor Solusi Khusus: Penjabaran manfaat KasKita untuk berbagai jenis UMKM (Sembako, Laundry, Warung Kuliner, Jasa).\n"
            "• Modul Edukasi Finansial: Menghadirkan artikel literasi keuangan UMKM (manajemen hutang bon, kedisiplinan pencatatan harian, pemisahan uang usaha vs pribadi) yang dapat dibaca penuh melalui modal popup interaktif.\n"
            "• Statistik Dinamis & Testimoni: Menampilkan jumlah pengguna terdaftar, jumlah transaksi tercatat, serta ulasan positif dari mitra UMKM."
        ),
        (
            "5.2 Modul Otentikasi & Auto-Seeding Kategori",
            "Menangani proses pintu masuk dan pendaftaran pemilik bisnis secara aman dan instan:\n"
            "• Registrasi Bisnis Komprehensif: Mengumpulkan data esensial seperti nama pemilik, username unik, alamat email, no. HP, nama usaha, serta kategori usaha.\n"
            "• Auto-Seeding 10 Kategori Standar: Segera setelah pengguna baru menyelesaikan pendaftaran, sistem secara otomatis menanamkan (seeding) 10 kategori finansial siap pakai ke akunnya (4 kategori pemasukan seperti Penjualan Produk, Pendapatan Jasa, Piutang Terbayar; dan 6 kategori pengeluaran seperti Kulakan Bahan, Gaji Karyawan, Sewa Tempat, Listrik/Air/Internet, Operasional Usaha, dll.) sehingga pengguna dapat langsung mencatat tanpa perlu repot membuat kategori dari nol.\n"
            "• Login Fleksibel (Username atau Email): Pengguna dapat masuk ke akun menggunakan username maupun alamat email aktif beserta password terenkripsi.\n"
            "• Otomasi Notifikasi Selamat Datang: Sistem otomatis mengirimkan pesan selamat datang pada pusat notifikasi akun baru."
        ),
        (
            "5.3 Dashboard Finansial Interaktif",
            "Pusat kendali dan analisa performa keuangan yang memberikan gambaran kesehatan kas secara seketika:\n"
            "• Kartu Metrik Utama: Menampilkan Total Saldo Kas Saat Ini, Total Pemasukan Bulan Berjalan, Total Pengeluaran Bulan Berjalan, serta Frekuensi Jumlah Transaksi.\n"
            "• Indikator Pertumbuhan (Growth Percentage): Membandingkan performa pemasukan dan pengeluaran bulan ini terhadap bulan lalu secara persentase matematis.\n"
            "• Grafik Multi-Dimensi Arus Kas (Chart.js): Menyediakan 3 opsi tab penayangan grafik:\n"
            "   1. Tren Harian: Analisa arus kas masuk vs keluar selama 30 hari terakhir.\n"
            "   2. Tren Bulanan: Perbandingan omzet dan biaya operasional selama 12 bulan terakhir.\n"
            "   3. Tren Tahunan: Prospek pertumbuhan kas selama 5 tahun terakhir.\n"
            "• Diagram Komposisi Pengeluaran (Doughnut Chart): Memetakan pos belanja usaha terbesar pada bulan berjalan untuk mendeteksi pembengkakan biaya operasional.\n"
            "• Tabel Transaksi Terakhir & Tombol Cepat: Menampilkan 6 catatan mutasi paling baru serta tombol modal 'Catat Transaksi' yang dapat dibuka langsung tanpa berpindah halaman."
        ),
        (
            "5.4 Modul Pencatatan Transaksi (Buku Kas Masuk & Keluar)",
            "Jantung fungsional KasKita untuk mencatat segala bentuk aliran dana tunai maupun perbankan:\n"
            "• Formulir Input Validatif: Input jenis kas (Pemasukan/Pengeluaran), pilihan kategori dinamis, nominal rupiah terformat, tanggal transaksi, deskripsi kegiatan, dan catatan rincian.\n"
            "• Sistem Filter Canggih (Multi-parameter): Pengguna dapat menyaring data berdasarkan jenis transaksi, kategori tertentu, rentang tanggal awal & akhir, serta fitur pencarian teks instan (keyword search) pada kolom deskripsi dan catatan.\n"
            "• Kartu Ringkasan Rekapitulasi Real-Time: Otomatis menghitung ulang total uang masuk, total uang keluar, dan surplus/defisit bersih hanya untuk baris data yang sedang tampil berdasarkan filter aktif.\n"
            "• Operasi CRUD Lengkap: Pengguna dapat mengubah (edit) rincian transaksi atau menghapusnya jika terjadi kesalahan pencatatan, disertai modal konfirmasi pengamanan."
        ),
        (
            "5.5 Modul Manajemen Kategori Kustom",
            "Fleksibilitas penuh bagi pelaku usaha untuk menyesuaikan pos pembukuan dengan karakteristik unik tokonya:\n"
            "• Pemisahan Tab Pemasukan & Pengeluaran: Antarmuka teratur yang memisahkan pos aliran dana masuk dan pos aliran dana keluar.\n"
            "• Personalisasi Visual: Pemilik usaha dapat memilih nama pos, menentukan icon grafis (Bootstrap Icons), serta memilih kode warna aksen kategori.\n"
            "• Analisa Akumulasi Finansial per Kategori: Setiap kartu kategori menyajikan informasi jumlah transaksi yang pernah terjadi (withCount) serta total akumulasi rupiah yang berputar di pos tersebut (withSum)."
        ),
        (
            "5.6 Modul Laporan Keuangan, Ekspor Excel, dan Cetak Nota",
            "Fasilitas rekapitulasi komprehensif untuk pertanggungjawaban usaha dan evaluasi berkala:\n"
            "• Filter Periode Fleksibel: Pilihan laporan berdasarkan bulan & tahun berjalan atau berdasarkan tanggal kustom (Custom Date Range).\n"
            "• Tabel Rekapitulasi per Kategori: Menghitung persentase kontribusi setiap pos kategori terhadap total arus kas periode tersebut.\n"
            "• Ekspor Spreadsheet Excel (.CSV): Mengunduh berkas laporan dalam format file tabel yang dilengkapi UTF-8 BOM (agar karakter rupiah dan teks bahasa Indonesia terbaca rapi di Microsoft Excel), kop nama usaha, identitas pemilik, rincian transaksi, dan baris total saldo akhir.\n"
            "• Fitur Cetak Laporan (Print Layout): Halaman khusus siap cetak (print-ready stylesheet) yang menghilangkan elemen navigasi website dan menampilkan kop surat resmi UMKM, tabel mutasi rapi, serta kolom tanda tangan pemilik usaha."
        ),
        (
            "5.7 Modul Profil Pengguna & Pusat Notifikasi",
            "Pengelolaan identitas usaha dan riwayat aktivitas akun:\n"
            "• Manajemen Data Usaha: Pembaruan nama pemilik, email, nomor kontak, nama toko UMKM, dan bidang usaha.\n"
            "• Pengamanan Kata Sandi: Mengganti password lama dengan proteksi Hash::check terhadap password aktif saat ini dan validasi konfirmasi password baru minimal 8 karakter.\n"
            "• Pusat Notifikasi Interaktif: Riwayat pemberitahuan sistem dengan filter status belum dibaca, badge penanda warna, tombol tandai satu per satu, serta tombol 'Tandai Semua Telah Dibaca'."
        )
    ]

    for title, desc in modules:
        doc.add_heading(title, level=2)
        p = doc.add_paragraph()
        p.paragraph_format.line_spacing = 1.15
        p.paragraph_format.space_after = Pt(6)
        p.add_run(desc)

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAB 6: LOGIKA BISNIS & ALUR KERJA SISTEM (USER FLOW)
    # -------------------------------------------------------------
    h6 = doc.add_heading(level=1)
    r = h6.add_run("BAB 6: Logika Bisnis & Alur Kerja Sistem (User Flow)")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Alur interaksi pengguna dengan aplikasi KasKita dirancang sesederhana mungkin agar dapat diadopsi dengan mudah "
        "oleh pengguna yang belum pernah mempelajari ilmu akuntansi formal sekalipun. Berikut adalah tahapan siklus kerja sistem:"
    )

    flow_w = [Inches(1.2), Inches(1.8), Inches(3.5)]
    flow_headers = ["Fase Alur", "Tindakan Pengguna", "Respon & Pemrosesan Otomatis Sistem"]
    flow_data = [
        [
            "Tahap 1:\nRegistrasi",
            "Pemilik usaha mengisi formulir nama, email, username, no hp, nama usaha, dan password.",
            "Validasi unik email/username; enkripsi password; pembuatan row pengguna baru; auto-seeding 10 kategori default; auto-login langsung ke Dashboard."
        ],
        [
            "Tahap 2:\nEntri Transaksi",
            "Pengguna mencatat transaksi saat transaksi terjadi (misal: 'Penjualan Beras 50kg' senilai Rp 700.000).",
            "Validasi nominal numerik positif; penyimpanan data transaksi; penghitungan ulang total saldo kas usaha secara instan."
        ],
        [
            "Tahap 3:\nMonitoring Kas",
            "Membuka Dashboard KasKita setiap sore atau malam hari saat penutupan toko.",
            "Sistem mengagregasi data hari ini, menghitung total pemasukan harian vs pengeluaran operasional, serta menampilkan persentase margin keuntungan harian."
        ],
        [
            "Tahap 4:\nEvaluasi Bulanan",
            "Membuka menu Laporan Keuangan, memilih periode bulan berjalan.",
            "Sistem menyajikan rekapitulasi omzet kotor, pengeluaran kulakan, biaya gaji, utilitas, laba bersih, serta grafik pos pengeluaran terbesar."
        ],
        [
            "Tahap 5:\nPelaporan Eksternal",
            "Mengklik tombol 'Ekspor Excel' atau 'Cetak Laporan'.",
            "Sistem men-generate file CSV siap buka di Excel atau membuka dialog print dokumen bertanda tangan resmi untuk pengajuan pinjaman modal KUR/Bank."
        ]
    ]
    
    t_flow = doc.add_table(rows=1, cols=3)
    format_table(t_flow, flow_w, flow_headers, flow_data)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(8)
    
    add_callout(
        doc,
        "Rumus Dasar Kalkulasi Arus Kas KasKita:\n"
        "1. Saldo Kas Riil = Total Seluruh Pemasukan (All Time) - Total Seluruh Pengeluaran (All Time)\n"
        "2. Laba Bersih Periode = Total Pemasukan Periode Terpilih - Total Pengeluaran Periode Terpilih\n"
        "3. Pertumbuhan Omzet (%) = [ (Omzet Bulan Ini - Omzet Bulan Lalu) / Omzet Bulan Lalu ] * 100%",
        title="FORMULA MATEMATIS FINANSIAL KASKITA"
    )

    # -------------------------------------------------------------
    # BAB 7: FITUR KEAMANAN, VALIDASI, & INTEGRITAS DATA
    # -------------------------------------------------------------
    h7 = doc.add_heading(level=1)
    r = h7.add_run("BAB 7: Fitur Keamanan, Validasi, & Integritas Data")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Mengingat data yang disimpan merupakan informasi finansial rahasia milik pelaku bisnis, KasKita menerapkan "
        "berbagai lapisan keamanan standar enterprise untuk menjamin kerahasiaan dan integritas data:"
    )

    sec_w = [Inches(1.8), Inches(4.7)]
    sec_headers = ["Mekanisme Proteksi", "Implementasi Teknis & Perlindungan yang Diberikan"]
    sec_data = [
        [
            "Enkripsi Password (Bcrypt Hash)",
            "Kata sandi pengguna tidak pernah disimpan dalam bentuk teks biasa (plain text), melainkan di-hash menggunakan algoritma Bcrypt dengan cost factor teruji. Bahkan administrator database tidak dapat membaca kata sandi asli pengguna."
        ],
        [
            "Proteksi CSRF (Cross-Site Request Forgery)",
            "Setiap formulir POST, PUT, dan DELETE dilindungi oleh token CSRF unik bawaan Laravel (@csrf) untuk mencegah serangan pemalsuan permintaan antar-situs dari pihak luar yang tidak berwenang."
        ],
        [
            "Pencegahan SQL Injection via Eloquent ORM",
            "Semua interaksi terhadap database menggunakan PDO Prepared Statements dan Eloquent Query Builder dengan parameter binding otomatis, sehingga karakter berbahaya masukan pengguna tidak dapat memanipulasi query SQL."
        ],
        [
            "Sanitasi Masukan & XSS Prevention",
            "Semua variabel data yang dirender pada Blade view menggunakan sintaks kurung kurawal ganda {{ }}, yang secara otomatis meloloskan (escapes) tag HTML dan skrip JavaScript berbahaya untuk menangkal ancaman Cross-Site Scripting."
        ],
        [
            "Proteksi Akses Middleware 'auth'",
            "Semua rute krusial (dashboard, transactions, categories, reports, profile, notifications) dipagari oleh middleware autentikasi. Pengguna yang belum terautentikasi otomatis dialihkan ke laman login."
        ],
        [
            "Validasi Input Bahasa Indonesia",
            "Setiap request diperiksa secara ketat di tingkat Controller sebelum dieksekusi. Pesan kesalahan disajikan dalam Bahasa Indonesia yang ramah dan mudah dimengerti pengguna, menghindari tampilan error teknis yang membingungkan."
        ]
    ]

    t_sec = doc.add_table(rows=1, cols=2)
    format_table(t_sec, sec_w, sec_headers, sec_data)

    doc.add_page_break()

    # -------------------------------------------------------------
    # BAB 8: PANDUAN INSTALASI & PENGOPERASIAN SERVER
    # -------------------------------------------------------------
    h8 = doc.add_heading(level=1)
    r = h8.add_run("BAB 8: Panduan Instalasi & Pengoperasian Server")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "Untuk menjalankan sistem KasKita di lingkungan pengembangan lokal (misalnya menggunakan XAMPP di Windows) "
        "maupun di server hosting/VPS produksi, ikuti panduan prosedur standar berikut:"
    )

    steps = [
        (
            "Langkah 1: Prasyarat Lingkungan (Environment Prerequisites)",
            "Pastikan sistem server atau komputer lokal telah terpasang:\n"
            "• Web Server: Apache atau Nginx\n"
            "• PHP: Versi 8.1 atau 8.2+ dengan ekstensi pdo, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json\n"
            "• Database: MySQL versi 5.7+ atau MariaDB versi 10.3+\n"
            "• Composer: Dependency Manager PHP versi 2.x"
        ),
        (
            "Langkah 2: Konfigurasi File Lingkungan (.env)",
            "Pastikan file konfigurasi '.env' telah terkonfigurasi dengan nama basis data yang tepat, contoh konfigurasi:\n"
            "DB_CONNECTION=mysql\n"
            "DB_HOST=127.0.0.1\n"
            "DB_PORT=3306\n"
            "DB_DATABASE=kaskita_umkm\n"
            "DB_USERNAME=root\n"
            "DB_PASSWORD="
        ),
        (
            "Langkah 3: Pembuatan Basis Data & Migrasi Skema",
            "Buka phpMyAdmin atau MySQL CLI, buat database baru dengan nama 'kaskita_umkm'. Kemudian jalankan perintah migrasi skema tabel di terminal project:\n"
            "> php artisan migrate\n"
            "Perintah ini akan secara otomatis membentuk tabel users, categories, transactions, notifications, dan tabel sistem pendukung lainnya."
        ),
        (
            "Langkah 4: Menjalankan Server Aplikasi",
            "Aplikasi dapat diakses langsung melalui virtual host Apache XAMPP (http://localhost/my-laravel-app/public) atau menggunakan web server internal artisan:\n"
            "> php artisan serve\n"
            "Setelah berjalan, buka browser pada alamat: http://127.0.0.1:8000"
        ),
        (
            "Langkah 5: Akun Pengujian & Pendaftaran",
            "Pengguna dapat langsung mengklik tombol 'Daftar Sekarang' di pojok kanan atas halaman beranda, melengkapi form profil UMKM, dan mulai mencatat transaksi finansial."
        )
    ]

    for title, desc in steps:
        doc.add_heading(title, level=2)
        p = doc.add_paragraph()
        p.paragraph_format.line_spacing = 1.15
        p.paragraph_format.space_after = Pt(6)
        p.add_run(desc)

    # -------------------------------------------------------------
    # BAB 9: KESIMPULAN & RENCANA PENGEMBANGAN LANJUTAN
    # -------------------------------------------------------------
    h9 = doc.add_heading(level=1)
    r = h9.add_run("BAB 9: Kesimpulan & Rencana Pengembangan Lanjutan")
    r.font.name = 'Segoe UI'
    r.font.size = Pt(16)
    r.font.color.rgb = RGBColor(20, 108, 67)
    
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(8)
    p.add_run(
        "KasKita berhasil menghadirkan solusi teknologi tepat guna yang menjawab kebutuhan mendesak jutaan pelaku "
        "UMKM dalam mengelola tata kelola keuangan usaha secara profesional, rapi, dan mudah diakses. "
        "Dengan antarmuka yang modern, responsif di seluruh layar ponsel dan komputer, serta fitur laporan kas yang "
        "akurat, KasKita mampu meningkatkan literasi finansial pengusaha kecil serta membantu usaha mereka bertumbuh "
        "secara berkelanjutan (sustainable growth)."
    )

    doc.add_heading("Peta Jalan Pengembangan Mendatang (Future Roadmap)", level=2)
    
    road_w = [Inches(1.5), Inches(2.0), Inches(3.0)]
    road_headers = ["Fitur Mendatang", "Target Waktu", "Dampak & Nilai Tambah"]
    road_data = [
        [
            "Modul Kasir POS (Point of Sale)",
            "Kuartal 1 2027",
            "Integrasi langsung antara penjualan barang di meja kasir dengan pencatatan kas otomatis di modul KasKita tanpa perlu input ganda."
        ],
        [
            "Multi-User & Hak Akses Kasir/Karyawan",
            "Kuartal 2 2027",
            "Pemilik usaha dapat membuatkan akun staf dengan pembatasan hak akses (hanya boleh mencatat kas masuk tanpa bisa melihat total saldo rahasia pemilik)."
        ],
        [
            "Pengingat Piutang via WhatsApp API",
            "Kuartal 3 2027",
            "Pengiriman pesan pengingat jatuh tempo bon pelanggan secara otomatis ke nomor WhatsApp pelanggan yang berhutang."
        ],
        [
            "Aplikasi Mobile Native (Android & iOS)",
            "Kuartal 4 2027",
            "Peluncuran aplikasi mobile di Google Play Store dengan fitur pemindaian OCR nota belanja menggunakan kamera ponsel."
        ]
    ]

    t_road = doc.add_table(rows=1, cols=3)
    format_table(t_road, road_w, road_headers, road_data)

    doc.add_paragraph().paragraph_format.space_after = Pt(16)
    
    # Penutup Dokumen
    p_end = doc.add_paragraph()
    p_end.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_end = p_end.add_run("--- Akhir dari Dokumen Penjelasan Sistem KasKita UMKM ---")
    r_end.font.name = 'Segoe UI'
    r_end.font.size = Pt(10)
    r_end.font.italic = True
    r_end.font.color.rgb = RGBColor(148, 163, 184)

    # Save document
    output_filename = "Dokumentasi_Sistem_KasKita_UMKM.docx"
    doc.save(output_filename)
    print(f"Document successfully created: {os.path.abspath(output_filename)}")

if __name__ == "__main__":
    build_documentation()
