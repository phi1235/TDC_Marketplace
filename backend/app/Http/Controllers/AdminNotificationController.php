<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    // Tạo mới 1 thông báo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $notification = AdminNotification::create([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'] ?? 'system',
            'title' => $validated['title'],
            'message' => $validated['message'],
            'data' => $validated['data'] ?? null,
        ]);

        return response()->json([
            'message' => 'Tạo thông báo thành công',
            'data' => $notification
        ], 201);
    }

    // Lấy danh sách thông báo của user
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = AdminNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($notifications);
    }
}
