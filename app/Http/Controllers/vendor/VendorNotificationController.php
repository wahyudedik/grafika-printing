<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Responses\FlashMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorNotificationController extends Controller
{
    /**
     * Display all notifications for the vendor
     */
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('vendor.notifications.index', compact('notifications'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications()->markAsRead();

        return FlashMessage::backSuccess('Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($notificationId): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return FlashMessage::backSuccess('Notifikasi telah ditandai sebagai dibaca.');
    }
}
