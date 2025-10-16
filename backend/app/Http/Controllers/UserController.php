<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // 🧠 Lấy thông tin hồ sơ người dùng
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // 🛠 Cập nhật hồ sơ người dùng
    public function update(Request $request)
    {
        $user = $request->user();

        // Xác thực dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Nếu có ảnh đại diện mới
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ (nếu có)
            if ($user->avatar && Storage::disk('public')->exists(str_replace('storage/', '', $user->avatar))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar));
            }

            // Lưu ảnh mới vào thư mục storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = asset('storage/' . $path);
        }

        // Cập nhật thông tin
        $user->update($validated);

        return response()->json([
            'message' => 'Cập nhật thành công!',
            'user' => $user
        ]);
    }
}
