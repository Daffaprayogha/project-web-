<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Notification;
use Carbon\Carbon;

class BillNotificationService
{
    /**
     * Synchronize and generate notifications for unpaid bills/expenses
     */
    public static function syncForUser(int $userId): void
    {
        $today = Carbon::today();

        $unpaidBills = Transaction::where('user_id', $userId)
            ->where('type', 'pengeluaran')
            ->where('status', 'belum_lunas')
            ->with('category')
            ->get();

        foreach ($unpaidBills as $tx) {
            $catName = $tx->category ? $tx->category->name : 'Pengeluaran';
            $formattedAmount = 'Rp ' . number_format($tx->amount, 0, ',', '.');
            
            $title = null;
            $message = null;
            $type = 'warning';

            if ($tx->due_date) {
                $dueDate = Carbon::parse($tx->due_date)->startOfDay();

                if ($dueDate->lt($today)) {
                    $daysOverdue = $dueDate->diffInDays($today);
                    $title = "⚠️ Tagihan Terlewat: {$tx->description}";
                    $message = "Tagihan [{$catName}] '{$tx->description}' sebesar {$formattedAmount} telah melewati tanggal bayar ({$dueDate->format('d/m/Y')}, terlambat {$daysOverdue} hari). Segera lakukan pelunasan!";
                    $type = 'warning';
                } elseif ($dueDate->isToday()) {
                    $title = "🔔 Jatuh Tempo Hari Ini: {$tx->description}";
                    $message = "Hari ini ({$dueDate->format('d/m/Y')}) adalah batas tanggal bayar tagihan [{$catName}] '{$tx->description}' sebesar {$formattedAmount}. Segera selesaikan pembayaran!";
                    $type = 'warning';
                } else {
                    $daysRemaining = $today->diffInDays($dueDate);
                    if ($daysRemaining <= 7) {
                        $title = "📅 Pengingat Bayar ({$daysRemaining} Hari Lagi): {$tx->description}";
                        $message = "Tagihan [{$catName}] '{$tx->description}' sebesar {$formattedAmount} jatuh tempo pada tanggal {$dueDate->format('d/m/Y')} ({$daysRemaining} hari lagi).";
                        $type = 'info';
                    } else {
                        $title = "📋 Tagihan Tercatat: {$tx->description}";
                        $message = "Tagihan [{$catName}] '{$tx->description}' sebesar {$formattedAmount} belum dibayar. Tanggal bayar: {$dueDate->format('d/m/Y')}.";
                        $type = 'info';
                    }
                }
            } else {
                $title = "📋 Tagihan Belum Dibayar: {$tx->description}";
                $message = "Pengeluaran [{$catName}] '{$tx->description}' sebesar {$formattedAmount} berstatus belum dibayar.";
                $type = 'warning';
            }

            if ($title && $message) {
                $notif = Notification::where('user_id', $userId)
                    ->where('transaction_id', $tx->id)
                    ->first();

                if ($notif) {
                    $notif->update([
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                    ]);
                } else {
                    Notification::create([
                        'user_id' => $userId,
                        'transaction_id' => $tx->id,
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'is_read' => false,
                    ]);
                }
            }
        }
    }

    /**
     * Mark bill as paid and update its notification
     */
    public static function markAsPaid(Transaction $tx): void
    {
        $tx->update(['status' => 'lunas']);

        $catName = $tx->category ? $tx->category->name : 'Pengeluaran';
        $formattedAmount = 'Rp ' . number_format($tx->amount, 0, ',', '.');

        // Update existing notification to success, or create paid confirmation
        $notif = Notification::where('user_id', $tx->user_id)
            ->where('transaction_id', $tx->id)
            ->first();

        if ($notif) {
            $notif->update([
                'title' => "✅ Tagihan Lunas: {$tx->description}",
                'message' => "Tagihan [{$catName}] '{$tx->description}' sebesar {$formattedAmount} telah berhasil dilunasi.",
                'type' => 'success',
                'is_read' => false,
            ]);
        } else {
            Notification::create([
                'user_id' => $tx->user_id,
                'transaction_id' => $tx->id,
                'title' => "✅ Tagihan Lunas: {$tx->description}",
                'message' => "Tagihan [{$catName}] '{$tx->description}' sebesar {$formattedAmount} telah berhasil dilunasi.",
                'type' => 'success',
                'is_read' => false,
            ]);
        }
    }
}
