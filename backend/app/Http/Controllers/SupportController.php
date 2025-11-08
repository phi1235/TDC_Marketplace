<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $to = config('mail.support_to', env('MAIL_SUPPORT_TO', 'support@tdc-marketplace.vn'));

        \Mail::send('emails.contact_message', [
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? null,
            'messageContent' => $data['message'],
        ], function ($m) use ($to, $data) {
            $m->to($to)->subject('[TDC Marketplace] Liên hệ: ' . ($data['subject'] ?? 'Không có chủ đề'));
        });

        return response()->json(['message' => 'Đã gửi email liên hệ'], 200);
    }
}
