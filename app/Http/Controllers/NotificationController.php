<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Transaction;
use App\Services\BillNotificationService;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();

        // Sync bill notifications so any overdue/today/upcoming bills are reflected
        BillNotificationService::syncForUser($userId);

        $unpaidBills = Transaction::where('user_id', $userId)
            ->where('status', 'belum_lunas')
            ->with('category')
            ->orderBy('due_date', 'asc')
            ->get();

        $notifications = Notification::where('user_id', $userId)
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications', 'unpaidBills'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}