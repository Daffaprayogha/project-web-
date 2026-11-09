<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KasKitaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Landing Page
     */
    public function test_01_landing_page_can_be_rendered()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('KasKita');
        $response->assertSee('Solusi Finansial UMKM');
    }

    /**
     * 2. Registrasi Akun Baru
     */
    public function test_02_user_registration_creates_account_and_default_categories()
    {
        $response = $this->post('/register', [
            'name' => 'Pak Budi Hartono',
            'username' => 'budihartono',
            'email' => 'budihartono@example.com',
            'phone' => '081234567890',
            'business_name' => 'Toko Kelontong Budi',
            'business_type' => 'Retail Kelontong',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('username', 'budihartono')->first();
        $this->assertNotNull($user);
        $this->assertGreaterThan(0, $user->categories()->count());
        $this->assertGreaterThan(0, $user->notifications()->count());
    }

    /**
     * 3. Login dengan Username
     */
    public function test_03_login_with_username()
    {
        $user = User::factory()->create([
            'username' => 'pemilik_toko',
            'password' => Hash::make('secret12345'),
        ]);

        $response = $this->post('/login', [
            'login' => 'pemilik_toko',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 4. Login dengan Email
     */
    public function test_04_login_with_email()
    {
        $user = User::factory()->create([
            'email' => 'toko_sukses@example.com',
            'password' => Hash::make('secret12345'),
        ]);

        $response = $this->post('/login', [
            'login' => 'toko_sukses@example.com',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 5. Login dengan Password Salah
     */
    public function test_05_login_with_wrong_password_fails()
    {
        User::factory()->create([
            'username' => 'kasir_aman',
            'password' => Hash::make('secret12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'kasir_aman',
            'password' => 'passwordsalah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /**
     * 6. Logout Mengakhiri Sesi
     */
    public function test_06_logout_invalidates_session()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * 7. Proteksi Halaman dari Pengguna Belum Login
     */
    public function test_07_guest_cannot_access_protected_pages()
    {
        $protectedRoutes = [
            '/dashboard',
            '/transactions',
            '/categories',
            '/reports',
            '/profile',
            '/settings',
            '/notifications',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /**
     * 8. Tambah Transaksi Pemasukan dan Pengeluaran
     */
    public function test_08_store_transaction_income_and_expense()
    {
        $user = User::factory()->create();
        $catIncome = Category::create([
            'user_id' => $user->id,
            'name' => 'Penjualan Kue',
            'type' => 'pemasukan',
        ]);
        $catExpense = Category::create([
            'user_id' => $user->id,
            'name' => 'Beli Tepung',
            'type' => 'pengeluaran',
        ]);

        // Pemasukan
        $resIncome = $this->actingAs($user)->post('/transactions', [
            'type' => 'pemasukan',
            'category_id' => $catIncome->id,
            'amount' => 150000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Penjualan 10 loyang kue bolu',
        ]);
        $resIncome->assertSessionHas('success');

        // Pengeluaran
        $resExpense = $this->actingAs($user)->post('/transactions', [
            'type' => 'pengeluaran',
            'category_id' => $catExpense->id,
            'amount' => 75000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Beli 5 kg tepung terigu',
        ]);
        $resExpense->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 150000,
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'amount' => 75000,
        ]);
    }

    /**
     * 9. Edit Transaksi
     */
    public function test_09_update_transaction()
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'user_id' => $user->id,
            'name' => 'Kulakan Telur',
            'type' => 'pengeluaran',
        ]);

        $tx = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type' => 'pengeluaran',
            'amount' => 200000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Kulakan telur 5 peti',
        ]);

        $res = $this->actingAs($user)->put('/transactions/' . $tx->id, [
            'type' => 'pengeluaran',
            'category_id' => $cat->id,
            'amount' => 250000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Kulakan telur 6 peti revisi',
        ]);
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'id' => $tx->id,
            'amount' => 250000,
            'description' => 'Kulakan telur 6 peti revisi',
        ]);
    }

    /**
     * 10. Hapus Transaksi
     */
    public function test_10_delete_transaction()
    {
        $user = User::factory()->create();
        $tx = Transaction::create([
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 300000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Transaksi salah input',
        ]);

        $res = $this->actingAs($user)->delete('/transactions/' . $tx->id);
        $res->assertSessionHas('success');
        $this->assertDatabaseMissing('transactions', ['id' => $tx->id]);
    }

    /**
     * 11. Validasi Nominal Transaksi (Harus Angka > 0)
     */
    public function test_11_transaction_nominal_validation()
    {
        $user = User::factory()->create();

        // Amount <= 0
        $resZero = $this->actingAs($user)->post('/transactions', [
            'type' => 'pemasukan',
            'amount' => 0,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Test nominal 0',
        ]);
        $resZero->assertSessionHasErrors('amount');

        // Negative Amount
        $resNegative = $this->actingAs($user)->post('/transactions', [
            'type' => 'pemasukan',
            'amount' => -50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Test nominal negatif',
        ]);
        $resNegative->assertSessionHasErrors('amount');

        // Non-numeric amount
        $resText = $this->actingAs($user)->post('/transactions', [
            'type' => 'pemasukan',
            'amount' => 'seratusribu',
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Test nominal teks',
        ]);
        $resText->assertSessionHasErrors('amount');
    }

    /**
     * 12. Validasi Tanggal Transaksi
     */
    public function test_12_transaction_date_validation()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->post('/transactions', [
            'type' => 'pemasukan',
            'amount' => 50000,
            'transaction_date' => 'bukan-tanggal-valid',
            'description' => 'Test tanggal invalid',
        ]);
        $res->assertSessionHasErrors('transaction_date');
    }

    /**
     * 13. Validasi Kategori (Tipe Kategori Harus Cocok dengan Jenis Transaksi)
     */
    public function test_13_transaction_category_type_mismatch_rejected()
    {
        $user = User::factory()->create();
        $catExpense = Category::create([
            'user_id' => $user->id,
            'name' => 'Operasional Listrik',
            'type' => 'pengeluaran',
        ]);

        // Assigning expense category to income transaction must fail
        $res = $this->actingAs($user)->post('/transactions', [
            'type' => 'pemasukan',
            'category_id' => $catExpense->id,
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Salah kategori pos',
        ]);

        $res->assertSessionHasErrors('category_id');
    }

    /**
     * 14. Pencegahan Akses & Modifikasi Transaksi Pengguna Lain
     */
    public function test_14_cannot_access_or_modify_other_user_transaction()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $txUserA = Transaction::create([
            'user_id' => $userA->id,
            'type' => 'pemasukan',
            'amount' => 999000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Rahasia dagang User A',
        ]);

        // User B tries to update User A's transaction -> 404
        $resUpdate = $this->actingAs($userB)->put('/transactions/' . $txUserA->id, [
            'type' => 'pemasukan',
            'amount' => 1000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Hacked by User B',
        ]);
        $resUpdate->assertStatus(404);

        // User B tries to delete User A's transaction -> 404
        $resDelete = $this->actingAs($userB)->delete('/transactions/' . $txUserA->id);
        $resDelete->assertStatus(404);

        $this->assertDatabaseHas('transactions', [
            'id' => $txUserA->id,
            'amount' => 999000,
        ]);
    }

    /**
     * 15. Pencegahan Penggunaan Kategori Pengguna Lain
     */
    public function test_15_cannot_use_other_user_category()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $catUserA = Category::create([
            'user_id' => $userA->id,
            'name' => 'Kategori Khusus User A',
            'type' => 'pemasukan',
        ]);

        // User B attempts to use User A's category
        $res = $this->actingAs($userB)->post('/transactions', [
            'type' => 'pemasukan',
            'category_id' => $catUserA->id,
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Mencoba pakai kategori orang lain',
        ]);

        $res->assertSessionHasErrors('category_id');
    }

    /**
     * 16. Akurasi Perhitungan Keuangan: Pemasukan, Pengeluaran, Saldo Bersih, Saldo Negatif
     */
    public function test_16_financial_calculation_accuracy_income_expense_and_balance()
    {
        $user = User::factory()->create();

        // 1. Only income
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 100000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Pemasukan 1',
        ]);
        $this->assertEquals(100000, $user->fresh()->balance);

        // 2. Add expense: income > expense
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'amount' => 40000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Pengeluaran 1',
        ]);
        $this->assertEquals(60000, $user->fresh()->balance);

        // 3. Add large expense: expense > income (defisit / negative balance)
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'amount' => 100000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Pengeluaran besar defisit',
        ]);
        $this->assertEquals(-40000, $user->fresh()->balance);

        // Check dashboard response displays accurate balance
        $dashboardRes = $this->actingAs($user)->get('/dashboard');
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('data-target="-40000"', false);
    }

    /**
     * 17. Filter Transaksi (Jenis, Kategori, Tanggal, dan Pencarian)
     */
    public function test_17_transaction_filtering_by_type_category_date_and_search()
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'user_id' => $user->id,
            'name' => 'Kopi Robusta',
            'type' => 'pemasukan',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type' => 'pemasukan',
            'amount' => 88000,
            'transaction_date' => '2026-05-10',
            'description' => 'Kopi Arabika Toraja',
        ]);
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'amount' => 33000,
            'transaction_date' => '2026-05-15',
            'description' => 'Gula Pasir Karungan',
        ]);

        // Filter by Search
        $resSearch = $this->actingAs($user)->get('/transactions?search=Toraja');
        $resSearch->assertSee('Kopi Arabika Toraja');
        $resSearch->assertDontSee('Gula Pasir Karungan');

        // Filter by Type
        $resType = $this->actingAs($user)->get('/transactions?type=pengeluaran');
        $resType->assertSee('Gula Pasir Karungan');
        $resType->assertDontSee('Kopi Arabika Toraja');

        // Filter by Date
        $resDate = $this->actingAs($user)->get('/transactions?start_date=2026-05-01&end_date=2026-05-12');
        $resDate->assertSee('Kopi Arabika Toraja');
        $resDate->assertDontSee('Gula Pasir Karungan');
    }

    /**
     * 18. Filter Laporan Keuangan (Bulan, Tahun, Rentang Kustom, dan Jenis)
     */
    public function test_18_report_filtering_by_month_year_date_range_and_type()
    {
        $user = User::factory()->create();

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 120000,
            'transaction_date' => '2026-08-10',
            'description' => 'Omset Agustus 2026',
        ]);
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'amount' => 45000,
            'transaction_date' => '2026-08-15',
            'description' => 'Biaya Agustus 2026',
        ]);

        // Monthly Filter
        $resMonth = $this->actingAs($user)->get('/reports?month=08&year=2026');
        $resMonth->assertStatus(200);
        $resMonth->assertSee('Omset Agustus 2026');
        $resMonth->assertSee('Biaya Agustus 2026');

        // Type Filter (pemasukan saja)
        $resType = $this->actingAs($user)->get('/reports?month=08&year=2026&type=pemasukan');
        $resType->assertSee('Omset Agustus 2026');
        $resType->assertDontSee('Biaya Agustus 2026');

        // Inverted date range redirects with error
        $resInverted = $this->actingAs($user)->get('/reports?start_date=2026-08-20&end_date=2026-08-10');
        $resInverted->assertRedirect('/reports');
        $resInverted->assertSessionHas('error');
    }

    /**
     * 19. Ekspor CSV: UTF-8 BOM dan Proteksi CSV Formula Injection
     */
    public function test_19_csv_export_includes_bom_and_formula_injection_sanitization()
    {
        $user = User::factory()->create([
            'business_name' => '=HYPERLINK("http://evil.com")',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => '+cmd|"/C calc"!A0',
            'notes' => '@SUM(1+1)',
        ]);

        $response = $this->actingAs($user)->get('/reports/export-excel');
        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('content-type'));

        // Capture CSV stream output
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Check for UTF-8 BOM
        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $this->assertStringStartsWith($bom, $content);

        // Check that formula injection starters are sanitized with leading single quote
        $this->assertStringContainsString("'+cmd|\"\"/C calc\"\"!A0", $content);
        $this->assertStringContainsString("'@SUM(1+1)", $content);
        $this->assertStringContainsString("'=HYPERLINK(\"\"http://evil.com\"\")", $content);
    }

    /**
     * 20. Pratinjau Cetak Laporan Keuangan
     */
    public function test_20_report_print_view_renders_correctly()
    {
        $user = User::factory()->create([
            'business_name' => 'Toko Barokah Utama',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 500000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Penjualan Cetak PDF',
        ]);

        $response = $this->actingAs($user)->get('/reports/print');
        $response->assertStatus(200);
        $response->assertSee('Toko Barokah Utama');
        $response->assertSee('Penjualan Cetak PDF');
        $response->assertSee('LAPORAN KAS USAHA');
    }

    /**
     * 21. Pengelolaan Kategori (CRUD, Keunikan Nama, dan Proteksi Tipe jika Ada Transaksi)
     */
    public function test_21_category_management_crud_uniqueness_and_type_protection()
    {
        $user = User::factory()->create();

        // 1. Create Category
        $resStore = $this->actingAs($user)->post('/categories', [
            'name' => 'Belanja Modal Biji Kopi',
            'type' => 'pengeluaran',
            'icon' => 'bi-box-seam',
            'color' => '#dc3545',
        ]);
        $resStore->assertSessionHas('success');

        $cat = Category::where('user_id', $user->id)->where('name', 'Belanja Modal Biji Kopi')->first();
        $this->assertNotNull($cat);

        // 2. Duplicate Category with same type is rejected
        $resDuplicate = $this->actingAs($user)->post('/categories', [
            'name' => 'Belanja Modal Biji Kopi',
            'type' => 'pengeluaran',
        ]);
        $resDuplicate->assertSessionHasErrors('name');

        // 3. Attach transaction to this category
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type' => 'pengeluaran',
            'amount' => 120000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Beli biji kopi arabika',
        ]);

        // 4. Changing category type when transactions exist MUST fail
        $resTypeChange = $this->actingAs($user)->put('/categories/' . $cat->id, [
            'name' => 'Belanja Modal Biji Kopi',
            'type' => 'pemasukan', // Changed from pengeluaran!
        ]);
        $resTypeChange->assertSessionHasErrors('type');

        // 5. Updating name without changing type succeeds
        $resUpdate = $this->actingAs($user)->put('/categories/' . $cat->id, [
            'name' => 'Kulakan Biji Kopi Premium',
            'type' => 'pengeluaran',
        ]);
        $resUpdate->assertSessionHas('success');
        $this->assertEquals('Kulakan Biji Kopi Premium', $cat->fresh()->name);

        // 6. Delete category retains transaction with null category
        $resDelete = $this->actingAs($user)->delete('/categories/' . $cat->id);
        $resDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => null,
            'description' => 'Beli biji kopi arabika',
        ]);
    }

    /**
     * 22. Pengelolaan Profil dan Ubah Password
     */
    public function test_22_profile_and_password_update_with_uniqueness_and_hash_check()
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'username' => 'user_satu',
            'password' => Hash::make('oldpassword123'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'username' => 'user_dua',
        ]);

        // 1. Updating profile with existing another user's email fails
        $resEmailDup = $this->actingAs($user1)->put('/profile', [
            'name' => 'User Satu Update',
            'username' => 'user_satu',
            'email' => 'user2@example.com',
            'phone' => '0811111111',
            'business_name' => 'Usaha Satu',
        ]);
        $resEmailDup->assertSessionHasErrors('email');

        // 2. Updating with own data succeeds
        $resProfile = $this->actingAs($user1)->put('/profile', [
            'name' => 'User Satu Sukses',
            'username' => 'user_satu',
            'email' => 'user1_baru@example.com',
            'phone' => '08122223333',
            'business_name' => 'Warung Usaha Baru',
        ]);
        $resProfile->assertSessionHas('success');
        $this->assertEquals('User Satu Sukses', $user1->fresh()->name);

        // 3. Changing password with wrong current password fails
        $resWrongPass = $this->actingAs($user1)->put('/profile/password', [
            'current_password' => 'wrongpass',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $resWrongPass->assertSessionHasErrors('current_password');

        // 4. Changing password with correct current password succeeds
        $resRightPass = $this->actingAs($user1)->put('/profile/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $resRightPass->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpassword123', $user1->fresh()->password));
    }

    /**
     * 23. Pengelolaan Notifikasi (Daftar, Tandai Dibaca, Tandai Semua)
     */
    public function test_23_notifications_index_mark_as_read_and_mark_all()
    {
        $user = User::factory()->create();

        $n1 = Notification::create([
            'user_id' => $user->id,
            'title' => 'Notif 1',
            'message' => 'Pesan 1',
            'is_read' => false,
        ]);
        $n2 = Notification::create([
            'user_id' => $user->id,
            'title' => 'Notif 2',
            'message' => 'Pesan 2',
            'is_read' => false,
        ]);

        $resIndex = $this->actingAs($user)->get('/notifications');
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Notif 1');
        $resIndex->assertSee('Notif 2');

        // Mark single as read
        $this->actingAs($user)->post('/notifications/' . $n1->id . '/read');
        $this->assertTrue((bool)$n1->fresh()->is_read);
        $this->assertFalse((bool)$n2->fresh()->is_read);

        // Mark all as read
        $this->actingAs($user)->post('/notifications/read-all');
        $this->assertTrue((bool)$n2->fresh()->is_read);
    }

    /**
     * 24. Halaman Pengaturan & Cadangan JSON Aman
     */
    public function test_24_settings_page_and_safe_json_backup()
    {
        $user = User::factory()->create([
            'business_name' => 'Bakso Sedap Malam',
            'password' => 'supersecretpass',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'pemasukan',
            'amount' => 350000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Penjualan bakso urat',
        ]);

        $settingsResponse = $this->actingAs($user)->get('/settings');
        $settingsResponse->assertStatus(200);
        $settingsResponse->assertSee('Cadangan', false);

        $backupResponse = $this->actingAs($user)->get('/settings/backup-json');
        $backupResponse->assertStatus(200);
        $this->assertEquals('application/json', $backupResponse->headers->get('content-type'));

        $json = json_decode($backupResponse->getContent(), true);
        $this->assertEquals('KasKita UMKM', $json['app']);
        $this->assertEquals('Bakso Sedap Malam', $json['business']['name']);
        $this->assertCount(1, $json['transactions']);
        $this->assertEquals(350000, $json['transactions'][0]['amount']);

        // Must not expose password or sensitive internal keys
        $backupString = $backupResponse->getContent();
        $this->assertStringNotContainsString('supersecretpass', $backupString);
        $this->assertStringNotContainsString('remember_token', $backupString);
    }

    /**
     * 25. Bukti Transaksi: Unggah, Pembaruan, dan Penghapusan
     */
    public function test_25_receipt_upload_update_and_removal()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file1 = UploadedFile::fake()->create('nota_awal.jpg', 120, 'image/jpeg');

        // 1. Upload receipt on store
        $this->actingAs($user)->post('/transactions', [
            'type' => 'pengeluaran',
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Beli gas elpiji 3kg',
            'receipt_image' => $file1,
        ]);

        $tx = Transaction::where('user_id', $user->id)->first();
        $this->assertNotNull($tx->receipt_image);
        Storage::disk('public')->assertExists($tx->receipt_image);
        $oldPath = $tx->receipt_image;

        // 2. Replace receipt on update
        $file2 = UploadedFile::fake()->create('nota_baru.png', 150, 'image/png');
        $this->actingAs($user)->put('/transactions/' . $tx->id, [
            'type' => 'pengeluaran',
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Beli gas elpiji 3kg (nota baru)',
            'receipt_image' => $file2,
        ]);

        $tx->refresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($tx->receipt_image);

        // 3. Remove receipt without uploading new one
        $this->actingAs($user)->put('/transactions/' . $tx->id, [
            'type' => 'pengeluaran',
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Beli gas elpiji 3kg (tanpa nota)',
            'remove_receipt' => 1,
        ]);

        $tx->refresh();
        $this->assertNull($tx->receipt_image);

        // 4. Deleting transaction cleans up receipt if present
        $file3 = UploadedFile::fake()->create('nota_akhir.jpg', 80, 'image/jpeg');
        $this->actingAs($user)->put('/transactions/' . $tx->id, [
            'type' => 'pengeluaran',
            'amount' => 50000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'description' => 'Beli gas elpiji dengan nota final',
            'receipt_image' => $file3,
        ]);

        $tx->refresh();
        $finalPath = $tx->receipt_image;
        Storage::disk('public')->assertExists($finalPath);

        $this->actingAs($user)->delete('/transactions/' . $tx->id);
        $this->assertDatabaseMissing('transactions', ['id' => $tx->id]);
        Storage::disk('public')->assertMissing($finalPath);
    }

    /**
     * 26. Tagihan Belum Dibayar & Notifikasi Tanggal Bayar
     */
    public function test_26_unpaid_bill_with_due_date_creates_notification_and_can_be_marked_paid()
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'user_id' => $user->id,
            'name' => 'Listrik, Air & Internet',
            'type' => 'pengeluaran',
        ]);

        $dueDate = Carbon::now()->addDays(2)->format('Y-m-d');

        // 1. Simpan pengeluaran belum dibayar dengan tanggal bayar
        $res = $this->actingAs($user)->post('/transactions', [
            'type' => 'pengeluaran',
            'status' => 'belum_lunas',
            'category_id' => $cat->id,
            'amount' => 350000,
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'due_date' => $dueDate,
            'description' => 'Tagihan Listrik Toko Bulan Ini',
        ]);
        $res->assertSessionHas('success');

        $tx = Transaction::where('user_id', $user->id)->where('description', 'Tagihan Listrik Toko Bulan Ini')->first();
        $this->assertNotNull($tx);
        $this->assertEquals('belum_lunas', $tx->status);
        $this->assertEquals($dueDate, $tx->due_date->format('Y-m-d'));

        // 2. Cek apakah notifikasi otomatis terbuat di tabel notifications
        $notif = Notification::where('user_id', $user->id)->where('transaction_id', $tx->id)->first();
        $this->assertNotNull($notif);
        $this->assertStringContainsString('Tagihan Listrik Toko Bulan Ini', $notif->title);

        // 3. Cek halaman Notifikasi memuat tagihan belum dibayar dan tanggal bayar
        $notifPage = $this->actingAs($user)->get('/notifications');
        $notifPage->assertStatus(200);
        $notifPage->assertSee('Tagihan Listrik Toko Bulan Ini');
        $notifPage->assertSee('350.000');
        $notifPage->assertSee('Tandai Lunas');

        // 4. Tandai Lunas via route mark-paid
        $payRes = $this->actingAs($user)->post('/transactions/' . $tx->id . '/mark-paid');
        $payRes->assertSessionHas('success');

        $tx->refresh();
        $this->assertEquals('lunas', $tx->status);

        // Notifikasi diperbarui menjadi status lunas
        $notif->refresh();
        $this->assertEquals('success', $notif->type);
        $this->assertStringContainsString('Lunas', $notif->title);
    }
}
