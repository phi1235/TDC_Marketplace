<?php
// app/Http/Controllers/SupportController.php
namespace App\Http\Controllers;

use App\Mail\SupportContactMail;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SupportController extends Controller
{
    public function contact(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['required','email','max:255'],
            'topic'   => ['nullable','string','max:50'],
            'message' => ['required','string','min:10','max:5000'],
        ]);

        // lưu DB (tuỳ chọn)
        $support = SupportRequest::create([
            'user_id' => optional($request->user())->id,
            'name'    => $data['name'],
            'email'   => $data['email'],
            'topic'   => $data['topic'] ?? null,
            'message' => $data['message'],
            'status'  => 'open',
        ]);

        // Gửi mail đến support (Mailpit trong Sail)
        // chỉnh lại địa chỉ nhận nếu cần
        $to = config('mail.support_to', 'support@tdc-marketplace.vn');
        Mail::to($to)->send(new SupportContactMail($data));

        return response()->json([
            'message' => 'Gửi yêu cầu hỗ trợ thành công.',
            'id'      => $support->id,
        ], 201);
    }
}
