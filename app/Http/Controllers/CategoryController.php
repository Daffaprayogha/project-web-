<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();

        $incomeCategories = Category::where('user_id', $userId)
            ->where('type', 'pemasukan')
            ->withCount('transactions')
            ->withSum('transactions', 'amount')
            ->orderBy('name')
            ->get();

        $expenseCategories = Category::where('user_id', $userId)
            ->where('type', 'pengeluaran')
            ->withCount('transactions')
            ->withSum('transactions', 'amount')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(function ($query) use ($request) {
                    return $query->where('user_id', Auth::id())
                                 ->where('type', $request->type);
                }),
            ],
            'type' => 'required|in:pemasukan,pengeluaran',
            'icon' => 'nullable|string|max:50',
            'color' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama tersebut sudah ada pada jenis pos ini.',
            'type.required' => 'Pilih jenis kategori (pemasukan atau pengeluaran).',
            'color.regex' => 'Format kode warna tidak valid (contoh: #198754).',
        ]);

        Category::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'icon' => $request->icon ?? ($request->type === 'pemasukan' ? 'bi-wallet2' : 'bi-bag'),
            'color' => $request->color ?? ($request->type === 'pemasukan' ? '#198754' : '#dc3545'),
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::where('user_id', Auth::id())->findOrFail($id);

        if ($category->type !== $request->type && $category->transactions()->exists()) {
            return redirect()->back()->withErrors([
                'type' => 'Jenis kategori tidak dapat diubah karena kategori ini sudah digunakan oleh transaksi yang tercatat.',
            ])->withInput();
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(function ($query) use ($request) {
                    return $query->where('user_id', Auth::id())
                                 ->where('type', $request->type);
                })->ignore($category->id),
            ],
            'type' => 'required|in:pemasukan,pengeluaran',
            'icon' => 'nullable|string|max:50',
            'color' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama tersebut sudah ada pada jenis pos ini.',
            'type.required' => 'Pilih jenis kategori (pemasukan atau pengeluaran).',
            'color.regex' => 'Format kode warna tidak valid (contoh: #198754).',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'icon' => $request->icon ?? $category->icon,
            'color' => $request->color ?? $category->color,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
        $txCount = $category->transactions()->count();
        $category->delete();

        $message = $txCount > 0 
            ? "Kategori berhasil dihapus. Sebanyak {$txCount} transaksi terkait kini berstatus Tanpa Kategori."
            : "Kategori berhasil dihapus.";

        return redirect()->back()->with('success', $message);
    }
}