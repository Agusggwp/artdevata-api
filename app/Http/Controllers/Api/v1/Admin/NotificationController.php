<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Admin\NotificationController as WebNotificationController;
use App\Http\Controllers\Controller;
use App\Models\BusinessNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $notifications = WebNotificationController::getActiveNotifications();

        return $this->successResponse([
            'total_unread'  => $notifications->where('is_urgent', true)->count(),
            'notifications' => $notifications
        ], 'Notifikasi berhasil diambil.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        BusinessNotification::whereNull('read_at')->update(['read_at' => now()]);
        return $this->successResponse(null, 'Semua notifikasi ditandai telah dibaca.');
    }
}
