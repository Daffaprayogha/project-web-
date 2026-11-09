<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        $totalTransactions = Transaction::where('user_id', $user->id)->count();
        $totalCategories = Category::where('user_id', $user->id)->count();
        $totalIncomeAllTime = Transaction::where('user_id', $user->id)->where('type', 'pemasukan')->sum('amount');
        $totalExpenseAllTime = Transaction::where('user_id', $user->id)->where('type', 'pengeluaran')->sum('amount');
        $netBalanceAllTime = $totalIncomeAllTime - $totalExpenseAllTime;

        return view('settings.index', compact(
            'user',
            'totalTransactions',
            'totalCategories',
            'totalIncomeAllTime',
            'totalExpenseAllTime',
            'netBalanceAllTime'
        ));
    }

    public function exportJson()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        $transactions = Transaction::where('user_id', $user->id)->with('category')->get();

        $backupData = [
            'app' => 'KasKita UMKM',
            'version' => '1.0.0',
            'export_date' => Carbon::now()->toIso8601String(),
            'business' => [
                'name' => $user->business_name,
                'owner' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'business_type' => $user->business_type,
            ],
            'categories' => $categories->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'type' => $c->type,
                    'icon' => $c->icon,
                    'color' => $c->color,
                ];
            }),
            'transactions' => $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'category' => $t->category->name ?? null,
                    'type' => $t->type,
                    'amount' => (float)$t->amount,
                    'transaction_date' => $t->transaction_date->format('Y-m-d'),
                    'description' => $t->description,
                    'notes' => $t->notes,
                ];
            }),
        ];

        $jsonContent = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $safeName = \Illuminate\Support\Str::slug($user->business_name, '_') ?: 'umkm';
        $fileName = 'Backup_KasKita_' . $safeName . '_' . date('Ymd_His') . '.json';

        return response($jsonContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}