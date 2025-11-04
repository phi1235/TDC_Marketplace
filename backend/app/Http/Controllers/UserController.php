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

    public function myActivities(Request $request)
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
