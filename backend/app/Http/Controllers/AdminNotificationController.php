<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    // 🔹 Lấy danh sách thông báo admin
    public function index()
    {
        $notifications = AdminNotification::latest()->get();

        return response()->json([
            'data' => $notifications
        ]);
    }

    // 🔹 Tạo mới 1 thông báo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string',
            'user_id' => 'nullable|integer', // nếu null => gửi cho tất cả user
        ]);

        // Lưu thông báo admin
        $notification = AdminNotification::create($validated);

        // Nếu user_id == null => gửi cho tất cả user
        if (empty($validated['user_id'])) {
            // Gửi đến tất cả user
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => $validated['type'] ?? 'admin_broadcast',
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'data' => json_encode(['admin_notification_id' => $notification->id]),
                ]);
            }
        } else {
            // Gửi cho user cụ thể
            \App\Models\Notification::create([
                'user_id' => $validated['user_id'],
                'type' => $validated['type'] ?? 'admin_direct',
                'title' => $validated['title'],
                'message' => $validated['message'],
                'data' => json_encode(['admin_notification_id' => $notification->id]),
            ]);
        }

        return response()->json([
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }

    public function destroy($id)
    {
        $notification = AdminNotification::find($id);
        if (!$notification) {
            return response()->json(['message' => 'Không tìm thấy thông báo'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Xóa thông báo thành công']);
    }

}
