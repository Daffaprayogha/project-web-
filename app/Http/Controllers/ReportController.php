<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Validate date range if both provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            if ($request->start_date > $request->end_date) {
                return redirect()->route('reports.index')
                    ->with('error', 'Tanggal awal tidak boleh melebihi tanggal akhir.');
            }
        }

        // Default to current month if no filter
        $month = $request->get('month', Carbon::now()->format('m'));
        $year = $request->get('year', Carbon::now()->format('Y'));
        $type = $request->get('type', 'all');

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaction::where('transactions.user_id', $user->id)->with('category');

        if ($startDate && $endDate) {
            $query->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' s/d ' . Carbon::parse($endDate)->format('d M Y');
        } else {
            $query->whereYear('transactions.transaction_date', $year)->whereMonth('transactions.transaction_date', $month);
            $periodLabel = Carbon::createFromDate((int)$year, (int)$month, 1)->translatedFormat('F Y');
        }

        if ($type !== 'all' && in_array($type, ['pemasukan', 'pengeluaran'])) {
            $query->where('transactions.type', $type);
        }

        $transactions = (clone $query)->orderBy('transactions.transaction_date', 'asc')->get();

        if ($type === 'pemasukan') {
            $totalIncome = (float) $transactions->sum('amount');
            $totalExpense = 0.0;
        } elseif ($type === 'pengeluaran') {
            $totalIncome = 0.0;
            $totalExpense = (float) $transactions->sum('amount');
        } else {
            $totalIncome = (float) $transactions->where('type', 'pemasukan')->sum('amount');
            $totalExpense = (float) $transactions->where('type', 'pengeluaran')->sum('amount');
        }
        $netBalance = $totalIncome - $totalExpense;

        // Breakdown by Category
        $catQuery = Transaction::where('transactions.user_id', $user->id);
        if ($startDate && $endDate) {
            $catQuery->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
        } else {
            $catQuery->whereYear('transactions.transaction_date', $year)->whereMonth('transactions.transaction_date', $month);
        }
        if ($type !== 'all' && in_array($type, ['pemasukan', 'pengeluaran'])) {
            $catQuery->where('transactions.type', $type);
        }

        $categoryBreakdown = $catQuery
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                DB::raw('COALESCE(categories.name, "Tanpa Kategori") as category_name'),
                'transactions.type',
                DB::raw('COUNT(transactions.id) as count'),
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('category_name', 'transactions.type')
            ->orderByDesc('total')
            ->get();

        return view('reports.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'categoryBreakdown',
            'periodLabel',
            'month',
            'year',
            'startDate',
            'endDate',
            'type'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->format('m'));
        $year = $request->get('year', Carbon::now()->format('Y'));
        $type = $request->get('type', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Swap if inverted
        if ($startDate && $endDate && $startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $query = Transaction::where('transactions.user_id', $user->id)->with('category');

        if ($startDate && $endDate) {
            $query->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' s/d ' . Carbon::parse($endDate)->format('d M Y');
            $fileName = "Laporan_Keuangan_KasKita_{$startDate}_{$endDate}.csv";
        } else {
            $query->whereYear('transactions.transaction_date', $year)->whereMonth('transactions.transaction_date', $month);
            $periodLabel = Carbon::createFromDate((int)$year, (int)$month, 1)->translatedFormat('F Y');
            $fileName = "Laporan_Keuangan_KasKita_{$year}_{$month}.csv";
        }

        if ($type !== 'all' && in_array($type, ['pemasukan', 'pengeluaran'])) {
            $query->where('transactions.type', $type);
        }

        $transactions = $query->orderBy('transactions.transaction_date', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($transactions, $user, $periodLabel, $type) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Business Header with CSV Formula Injection Protection
            fputcsv($file, ['KasKita - Laporan Keuangan UMKM']);
            fputcsv($file, ['Nama Usaha', $this->sanitizeCsvValue($user->business_name)]);
            fputcsv($file, ['Pemilik', $this->sanitizeCsvValue($user->name)]);
            fputcsv($file, ['Periode', $periodLabel]);
            fputcsv($file, ['Filter Jenis', ucfirst($type)]);
            fputcsv($file, ['Tanggal Cetak', Carbon::now()->format('d/m/Y H:i')]);
            fputcsv($file, []);

            // Table headers
            fputcsv($file, ['No', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Catatan', 'Pemasukan (Rp)', 'Pengeluaran (Rp)']);

            $no = 1;
            $totInc = 0;
            $totExp = 0;

            foreach ($transactions as $tx) {
                $inc = $tx->type === 'pemasukan' ? (float)$tx->amount : 0.0;
                $exp = $tx->type === 'pengeluaran' ? (float)$tx->amount : 0.0;
                $totInc += $inc;
                $totExp += $exp;

                fputcsv($file, [
                    $no++,
                    $tx->transaction_date->format('d/m/Y'),
                    strtoupper($tx->type),
                    $this->sanitizeCsvValue($tx->category->name ?? 'Tanpa Kategori'),
                    $this->sanitizeCsvValue($tx->description),
                    $this->sanitizeCsvValue($tx->notes ?? '-'),
                    $inc,
                    $exp,
                ]);
            }

            // Summary row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL PEMASUKAN', '', $totInc, '']);
            fputcsv($file, ['', '', '', '', 'TOTAL PENGELUARAN', '', '', $totExp]);
            fputcsv($file, ['', '', '', '', 'SURPLUS / DEFISIT BERSIH', '', $totInc - $totExp, '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function print(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->format('m'));
        $year = $request->get('year', Carbon::now()->format('Y'));
        $type = $request->get('type', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate && $startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $query = Transaction::where('transactions.user_id', $user->id)->with('category');

        if ($startDate && $endDate) {
            $query->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            $periodLabel = Carbon::parse($startDate)->format('d F Y') . ' - ' . Carbon::parse($endDate)->format('d F Y');
        } else {
            $query->whereYear('transactions.transaction_date', $year)->whereMonth('transactions.transaction_date', $month);
            $periodLabel = Carbon::createFromDate((int)$year, (int)$month, 1)->translatedFormat('F Y');
        }

        if ($type !== 'all' && in_array($type, ['pemasukan', 'pengeluaran'])) {
            $query->where('transactions.type', $type);
        }

        $transactions = $query->orderBy('transactions.transaction_date', 'asc')->get();

        if ($type === 'pemasukan') {
            $totalIncome = (float) $transactions->sum('amount');
            $totalExpense = 0.0;
        } elseif ($type === 'pengeluaran') {
            $totalIncome = 0.0;
            $totalExpense = (float) $transactions->sum('amount');
        } else {
            $totalIncome = (float) $transactions->where('type', 'pemasukan')->sum('amount');
            $totalExpense = (float) $transactions->where('type', 'pengeluaran')->sum('amount');
        }
        $netBalance = $totalIncome - $totalExpense;

        return view('reports.print', compact(
            'transactions',
            'user',
            'periodLabel',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'type'
        ));
    }

    /**
     * Sanitize user input strings for CSV to prevent Spreadsheet Formula Injection (CWE-1236)
     */
    private function sanitizeCsvValue($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $str = (string) $value;
        // If string starts with =, +, -, @, tab, or carriage return, prepend a single quote
        if (preg_match('/^[\=\+\-\@\t\r]/', $str)) {
            return "'" . $str;
        }
        return $str;
    }
}
