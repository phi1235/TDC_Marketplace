<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    // Regex chỉ chấp nhận ngành TT (CNTT)
    private const EDU_REGEX = '/^\d{2}\d{3}tt\d{3,5}@tdc\.edu\.vn$/i';

    public function send(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email', Rule::regex(self::EDU_REGEX)],
        ], [
            'email.regex' => 'Email sinh viên phải theo mẫu YY + 3 số + TT + số @tdc.edu.vn',
        ]);

        $email = mb_strtolower($data['email']);

        // chống spam: nếu đã gửi <60s trước thì từ chối
        $last = EmailOtp::where('email',$email)->latest()->first();
        if ($last && $last->created_at->gt(now()->subSeconds(60))) {
            return response()->json(['message' => 'Vui lòng thử lại sau vài giây.'], 429);
        }

        // tạo mã 6 chữ số
        $code = (string) random_int(100000, 999999);

        // lưu (hash) + TTL 10 phút
        EmailOtp::create([
            'email'      => $email,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'used_at'    => null,
        ]);

        // gửi mail
        Mail::to($email)->send(new OtpCodeMail($code));

        return response()->json(['message' => 'Đã gửi mã xác thực.'], 200);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required','email', Rule::regex(self::EDU_REGEX)],
            'otp_code' => ['required','digits:6'],
        ]);

        $email = mb_strtolower($data['email']);

        $otp = EmailOtp::active()
            ->where('email',$email)
            ->latest()
            ->first();

        if (!$otp) {
            throw ValidationException::withMessages([
                'otp_code' => ['Mã không tồn tại hoặc đã hết hạn.'],
            ]);
        }

        if ($otp->attempts >= 5) {
            throw ValidationException::withMessages([
                'otp_code' => ['Bạn đã nhập sai quá số lần cho phép.'],
            ]);
        }

        // kiểm tra mã
        if (!Hash::check($data['otp_code'], $otp->code_hash)) {
            $otp->increment('attempts');
            throw ValidationException::withMessages([
                'otp_code' => ['Mã không đúng.'],
            ]);
        }

        // đánh dấu đã dùng
        $otp->update(['used_at' => now()]);

        return response()->json(['message' => 'Xác thực thành công.']);
    }

    /**
     * Hỗ trợ cho Register: kiểm tra otp inline
     */
    public static function assertEduOtpValid(string $email, ?string $otpCode): void
    {
        if (!preg_match(self::EDU_REGEX, $email)) {
            throw ValidationException::withMessages([
                'email' => ['Email sinh viên không đúng định dạng.'],
            ]);
        }
        if (!$otpCode) {
            throw ValidationException::withMessages([
                'otp_code' => ['Vui lòng nhập mã xác thực.'],
            ]);
        }

        $email = mb_strtolower($email);

        $otp = EmailOtp::active()
            ->where('email',$email)
            ->latest()
            ->first();

        if (!$otp || !Hash::check($otpCode, $otp->code_hash)) {
            throw ValidationException::withMessages([
                'otp_code' => ['Mã không đúng hoặc đã hết hạn.'],
            ]);
        }

        $otp->update(['used_at' => now()]);
    }
}