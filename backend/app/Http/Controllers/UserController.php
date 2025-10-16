<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
}
