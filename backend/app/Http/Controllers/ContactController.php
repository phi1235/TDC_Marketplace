<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * POST /api/support/contact
     * Gửi email liên hệ tới hộp thư hỗ trợ.
     */
    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        // Địa chỉ nhận hỗ trợ (cấu hình trong .env)
        $to = config('mail.support_to', env('MAIL_SUPPORT_TO', 'support@example.com'));
        $subject = $data['subject'] ?? 'Yêu cầu hỗ trợ mới';

        try {
            // Gửi email bằng view Blade
            Mail::send('emails.contact_message', [
                'name'    => $data['name'],
                'email'   => $data['email'],
                'subject' => $data['subject'] ?? null,
                'content' => $data['message'],
            ], function ($message) use ($to, $subject, $data) {
                $message->to($to)
                        ->subject($subject)
                        ->replyTo($data['email'], $data['name']);
            });

            return response()->json([
                'message' => 'Đã gửi email liên hệ',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Send contact mail failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gửi email thất bại, vui lòng thử lại sau.',
            ], 500);
        }
    }
}
