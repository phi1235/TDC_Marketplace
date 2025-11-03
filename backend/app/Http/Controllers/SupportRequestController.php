<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'topic'   => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $data['user_id'] = Auth::id();     // có thể null nếu chưa đăng nhập
        $data['status']  = 'open';

        $sr = SupportRequest::create($data);

        return response()->json([
            'message' => 'Gửi yêu cầu thành công',
            'data'    => $sr,
        ], 201);
    }
}
