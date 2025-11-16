<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class UserController extends Controller
{
    public function currentUser()
    {
        // Lấy user đầu tiên
        // $user = User::with('roles')->first();

        // return response()->json([
        //     'name' => $user->name,
        //     'role' => $user->roles->first()->name ?? 'user',
        // ]);
        $user = User::with('roles')->get();
        return response()->json($user->load('roles'));
    }
    //get all user
    public function allUsers()
    {
        try {
            $users = User::with('roles')->get(); // load tất cả user cùng roles
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //search user by id, name, email, role
    public function search(Request $request)
    {
        $keyword = $request->query('q'); // query param ?q=...
        
        $users = User::query()
            ->when($keyword, function($query, $keyword) {
                $query->where('id', 'like', "%{$keyword}%")
                      ->orWhere('name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%")
                      ->orWhere('phone', 'like', "%{$keyword}%");
            })
            ->get();

        return response()->json($users);
    }

    public function updateProfile(Request $request)
    {
        \Log::info('🔥 UPDATE PROFILE METHOD CALLED 🔥');
        
        $user = Auth::user();

        // Debug: Log request data
        \Log::info('Profile update request:', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'has_name' => $request->has('name'),
            'has_email' => $request->has('email'),
            'has_phone' => $request->has('phone'),
            'old_user_data' => $user->toArray()
        ]);

        // Validate input
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'major_id' => 'nullable|exists:majors,id'
        ]);

        // Cập nhật thông tin user
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('major_id')) {
            $user->major_id = $request->major_id;
        }

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Debug: Log before save
        \Log::info('Before save:', [
            'user_id' => $user->id,
            'user_data_before_save' => $user->toArray(),
            'user_dirty' => $user->getDirty()
        ]);

        $user->save();

        // Debug: Log after save
        \Log::info('After save:', [
            'user_id' => $user->id,
            'user_data_after_save' => $user->fresh()->toArray()
        ]);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'user' => $user
        ]);
    }    public function myActivities(Request $request)
    {
        $userId = Auth::id();
        $perPage = (int) $request->get('per_page', 20);
        $query = AuditLog::where('auditable_type', 'user')
            ->where('auditable_id', $userId);
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        $logs = $query->orderByDesc('created_at')->paginate($perPage);
        // Ẩn bớt thông tin nhạy cảm cho phía user
        $logs->getCollection()->transform(function ($log) {
            unset($log->user_agent);
            return $log;
        });
        return response()->json($logs);
    }
}
