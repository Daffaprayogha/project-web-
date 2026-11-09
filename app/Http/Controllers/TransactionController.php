<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\BillNotificationService;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Sync bill notifications for unpaid items
        BillNotificationService::syncForUser($user->id);

        $query = Transaction::where('user_id', $user->id)->with('category');

        // Filters
        if ($request->filled('type') && in_array($request->type, ['pemasukan', 'pengeluaran'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && in_array($request->status, ['lunas', 'belum_lunas'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Calculate summary for currently filtered records cleanly
        if ($request->filled('type') && in_array($request->type, ['pemasukan', 'pengeluaran'])) {
            if ($request->type === 'pemasukan') {
                $totalIncome = (float) (clone $query)->sum('amount');
                $totalExpense = 0.0;
            } else {
                $totalIncome = 0.0;
                $totalExpense = (float) (clone $query)->sum('amount');
            }
        } else {
            $totalIncome = (float) (clone $query)->where('type', 'pemasukan')->sum('amount');
            $totalExpense = (float) (clone $query)->where('type', 'pengeluaran')->sum('amount');
        }
        $netFiltered = $totalIncome - $totalExpense;

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'netFiltered'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pemasukan,pengeluaran',
            'status' => 'nullable|in:lunas,belum_lunas',
            'due_date' => 'nullable|date',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', Auth::id())
                                 ->where('type', $request->type);
                }),
            ],
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'type.required' => 'Pilih jenis transaksi (pemasukan atau pengeluaran).',
            'category_id.exists' => 'Kategori yang dipilih tidak valid, bukan milik Anda, atau jenisnya tidak sesuai.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib ditentukan.',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'receipt_image.image' => 'File bukti transaksi harus berupa gambar (JPG, PNG, WEBP).',
            'receipt_image.max' => 'Ukuran file bukti transaksi maksimal 3MB.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $status = $request->input('status', 'lunas');
        $dueDate = ($status === 'belum_lunas') ? $request->due_date : null;

        $tx = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'type' => $request->type,
            'status' => $status,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'due_date' => $dueDate,
            'description' => $request->description,
            'notes' => $request->notes,
            'receipt_image' => $receiptPath,
        ]);

        if ($status === 'belum_lunas') {
            BillNotificationService::syncForUser(Auth::id());
        }

        $msg = $status === 'belum_lunas' 
            ? 'Tagihan berhasil dicatat dan pengingat tanggal bayar telah masuk ke Notifikasi!' 
            : 'Transaksi kas berhasil dicatat!';

        return redirect()->back()->with('success', $msg);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'type' => 'required|in:pemasukan,pengeluaran',
            'status' => 'nullable|in:lunas,belum_lunas',
            'due_date' => 'nullable|date',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', Auth::id())
                                 ->where('type', $request->type);
                }),
            ],
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'type.required' => 'Pilih jenis transaksi (pemasukan atau pengeluaran).',
            'category_id.exists' => 'Kategori yang dipilih tidak valid, bukan milik Anda, atau jenisnya tidak sesuai.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib ditentukan.',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'receipt_image.image' => 'File bukti transaksi harus berupa gambar (JPG, PNG, WEBP).',
            'receipt_image.max' => 'Ukuran file bukti transaksi maksimal 3MB.',
        ]);

        $newStatus = $request->input('status', 'lunas');
        $newDueDate = ($newStatus === 'belum_lunas') ? $request->due_date : null;

        $data = [
            'category_id' => $request->category_id,
            'type' => $request->type,
            'status' => $newStatus,
            'due_date' => $newDueDate,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('receipt_image')) {
            if ($transaction->receipt_image && Storage::disk('public')->exists($transaction->receipt_image)) {
                Storage::disk('public')->delete($transaction->receipt_image);
            }
            $data['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        } elseif ($request->boolean('remove_receipt')) {
            if ($transaction->receipt_image && Storage::disk('public')->exists($transaction->receipt_image)) {
                Storage::disk('public')->delete($transaction->receipt_image);
            }
            $data['receipt_image'] = null;
        }

        $wasUnpaid = ($transaction->status === 'belum_lunas');
        $transaction->update($data);

        if ($wasUnpaid && $newStatus === 'lunas') {
            BillNotificationService::markAsPaid($transaction);
        } else {
            BillNotificationService::syncForUser(Auth::id());
        }

        return redirect()->back()->with('success', 'Transaksi kas berhasil diperbarui!');
    }

    public function markPaid($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        BillNotificationService::markAsPaid($transaction);

        return redirect()->back()->with('success', "Tagihan '{$transaction->description}' berhasil ditandai Lunas!");
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        if ($transaction->receipt_image && Storage::disk('public')->exists($transaction->receipt_image)) {
            Storage::disk('public')->delete($transaction->receipt_image);
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Transaksi kas berhasil dihapus.');
    }
}