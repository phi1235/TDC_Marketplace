<?php
// app/Http/Controllers/SupportRequestController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportRequest;

class SupportRequestController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'topic'   => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $data['user_id'] = optional($req->user())->id;
        $data['status']  = 'open';

        $support = SupportRequest::create($data);

        return response()->json([
            'message' => 'Gửi yêu cầu thành công',
            'data'    => $support,
        ], 201);
    }
}
