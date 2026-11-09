<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\BillNotificationService;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Sync bill notifications for unpaid items
        BillNotificationService::syncForUser($user->id);

        $unpaidBills = Transaction::where('user_id', $user->id)
            ->where('status', 'belum_lunas')
            ->with('category')
            ->orderBy('due_date', 'asc')
            ->get();

        // Financial Totals
        $allIncome = Transaction::where('user_id', $user->id)->where('type', 'pemasukan')->sum('amount');
        $allExpense = Transaction::where('user_id', $user->id)->where('type', 'pengeluaran')->sum('amount');
        $currentBalance = $allIncome - $allExpense;

        // Current Month
        $currentMonthIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->sum('amount');

        $currentMonthExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->sum('amount');

        $currentMonthNet = $currentMonthIncome - $currentMonthExpense;

        $currentMonthTxCount = Transaction::where('user_id', $user->id)
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->count();

        // Last Month Comparison
        $lastMonth = $now->copy()->subMonth();
        $lastMonthIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereYear('transaction_date', $lastMonth->year)
            ->whereMonth('transaction_date', $lastMonth->month)
            ->sum('amount');

        $lastMonthExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereYear('transaction_date', $lastMonth->year)
            ->whereMonth('transaction_date', $lastMonth->month)
            ->sum('amount');

        $incomeGrowth = $lastMonthIncome > 0 
            ? round((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 1) 
            : ($currentMonthIncome > 0 ? 100 : 0);

        $expenseGrowth = $lastMonthExpense > 0 
            ? round((($currentMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 1) 
            : ($currentMonthExpense > 0 ? 100 : 0);

        $indoMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // ========================================================
        // 1. DATA TREN ARUS KAS PER HARI (30 Hari Terakhir - Single Query)
        // ========================================================
        $startDate30 = $now->copy()->subDays(29)->format('Y-m-d');
        $dailyRecords = Transaction::where('user_id', $user->id)
            ->whereDate('transaction_date', '>=', $startDate30)
            ->selectRaw('DATE(transaction_date) as t_date, type, SUM(amount) as total')
            ->groupBy('t_date', 'type')
            ->get();

        $dailyMap = [];
        foreach ($dailyRecords as $row) {
            $key = Carbon::parse($row->t_date)->format('Y-m-d') . '_' . $row->type;
            $dailyMap[$key] = (float) $row->total;
        }

        $dailyLabels = [];
        $dailyIncome = [];
        $dailyExpense = [];

        for ($j = 29; $j >= 0; $j--) {
            $d = $now->copy()->subDays($j);
            $dateStr = $d->format('Y-m-d');
            $dayNum = (int) $d->format('n');
            $dayLabel = $d->format('d') . ' ' . ($indoMonths[$dayNum] ?? $d->format('M'));

            $dailyLabels[] = $dayLabel;
            $dailyIncome[] = $dailyMap[$dateStr . '_pemasukan'] ?? 0.0;
            $dailyExpense[] = $dailyMap[$dateStr . '_pengeluaran'] ?? 0.0;
        }

        // ========================================================
        // 2. DATA TREN ARUS KAS PER BULAN (12 Bulan Terakhir - Single Query)
        // ========================================================
        $startMonth12 = $now->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        if ($isSqlite) {
            $monthlyRecords = Transaction::where('user_id', $user->id)
                ->whereDate('transaction_date', '>=', $startMonth12)
                ->selectRaw("strftime('%Y', transaction_date) as t_year, cast(strftime('%m', transaction_date) as integer) as t_month, type, SUM(amount) as total")
                ->groupBy('t_year', 't_month', 'type')
                ->get();
        } else {
            $monthlyRecords = Transaction::where('user_id', $user->id)
                ->whereDate('transaction_date', '>=', $startMonth12)
                ->selectRaw('YEAR(transaction_date) as t_year, MONTH(transaction_date) as t_month, type, SUM(amount) as total')
                ->groupBy('t_year', 't_month', 'type')
                ->get();
        }

        $monthlyMap = [];
        foreach ($monthlyRecords as $row) {
            $key = $row->t_year . '_' . (int)$row->t_month . '_' . $row->type;
            $monthlyMap[$key] = (float) $row->total;
        }

        $chartMonths = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $monthNum = (int) $m->format('n');
            $yearNum = $m->format('y');
            $monthLabel = ($indoMonths[$monthNum] ?? $m->format('M')) . " '" . $yearNum;

            $chartMonths[] = $monthLabel;
            $chartIncome[] = $monthlyMap[$m->year . '_' . $monthNum . '_pemasukan'] ?? 0.0;
            $chartExpense[] = $monthlyMap[$m->year . '_' . $monthNum . '_pengeluaran'] ?? 0.0;
        }

        // ========================================================
        // 3. DATA TREN ARUS KAS PER TAHUN (5 Tahun Terakhir - Single Query)
        // ========================================================
        $currentYear = (int)$now->year;
        $startYear5 = $currentYear - 4;

        if ($isSqlite) {
            $yearlyRecords = Transaction::where('user_id', $user->id)
                ->whereRaw("cast(strftime('%Y', transaction_date) as integer) >= ?", [$startYear5])
                ->selectRaw("strftime('%Y', transaction_date) as t_year, type, SUM(amount) as total")
                ->groupBy('t_year', 'type')
                ->get();
        } else {
            $yearlyRecords = Transaction::where('user_id', $user->id)
                ->whereYear('transaction_date', '>=', $startYear5)
                ->selectRaw('YEAR(transaction_date) as t_year, type, SUM(amount) as total')
                ->groupBy('t_year', 'type')
                ->get();
        }

        $yearlyMap = [];
        foreach ($yearlyRecords as $row) {
            $key = $row->t_year . '_' . $row->type;
            $yearlyMap[$key] = (float) $row->total;
        }

        $yearlyLabels = [];
        $yearlyIncome = [];
        $yearlyExpense = [];

        for ($y = $startYear5; $y <= $currentYear; $y++) {
            $yearlyLabels[] = (string) $y;
            $yearlyIncome[] = $yearlyMap[$y . '_pemasukan'] ?? 0.0;
            $yearlyExpense[] = $yearlyMap[$y . '_pengeluaran'] ?? 0.0;
        }

        // Chart 2: Category Breakdown for Current Month Expense (Merah)
        $expenseCategories = Transaction::where('transactions.user_id', $user->id)
            ->where('transactions.type', 'pengeluaran')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                DB::raw('COALESCE(categories.name, "Tanpa Kategori") as category_name'),
                DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->groupBy('category_name')
            ->orderByDesc('total_amount')
            ->get();

        $catLabels = $expenseCategories->pluck('category_name')->toArray();
        $catValues = $expenseCategories->pluck('total_amount')->map(fn($v) => (float)$v)->toArray();

        // Recent Transactions (latest 6)
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        // Categories for quick modal
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();

        return view('dashboard.index', compact(
            'user',
            'currentBalance',
            'currentMonthNet',
            'currentMonthIncome',
            'currentMonthExpense',
            'currentMonthTxCount',
            'incomeGrowth',
            'expenseGrowth',
            'dailyLabels',
            'dailyIncome',
            'dailyExpense',
            'chartMonths',
            'chartIncome',
            'chartExpense',
            'yearlyLabels',
            'yearlyIncome',
            'yearlyExpense',
            'catLabels',
            'catValues',
            'recentTransactions',
            'categories',
            'unpaidBills'
        ));
    }
}