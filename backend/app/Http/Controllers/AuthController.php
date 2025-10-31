<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\EmailOtp; // <-- thêm
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\AuditLogService;

class AuthController extends Controller
{
    public function __construct(private AuditLogService $auditLogService) {}

    // Email sinh viên: YY + 3 số + TT + 3-5 số @tdc.edu.vn (không phân biệt hoa/thường)
    private const EDU_REGEX = '/^\d{2}\d{3}tt\d{3,5}@tdc\.edu\.vn$/i';

    public function register(RegisterRequest $request): JsonResponse
    {
        // Chuẩn hóa email
        $email = mb_strtolower($request->email);

        // Nếu email là @tdc.edu.vn theo mẫu => bắt buộc OTP
        if (preg_match(self::EDU_REGEX, $email)) {
            // Lấy otp_code từ request (RegisterView.vue đã gửi kèm)
            $otpCode = $request->input('otp_code');

            // Xác thực OTP (ném ValidationException nếu sai)
            $this->assertEduOtpValid($email, $otpCode);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $email,
            'password'  => Hash::make($request->password),
            'role'      => 'user',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // audit log
        $this->auditLogService->log($user, 'user_created', null, $user->toArray());

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        // Tạm thời disable rate limiting để test
        // $key = 'login:' . $request->ip();
        // if (RateLimiter::tooManyAttempts($key, 5)) {
        //     $seconds = RateLimiter::availableIn($key);
        //     throw ValidationException::withMessages([
        //         'email' => ["Bạn đã đăng nhập sai quá nhiều lần, thử lại sau {$seconds} giây."],
        //     ]);
        // }

        if (!Auth::attempt($request->only('email', 'password'))) {
            // RateLimiter::hit($key, 600); // 10 phút
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản đã bị khóa, liên hệ quản trị viên.'],
            ]);
        }

        // RateLimiter::clear($key);

        $user->update([
            'last_login_at' => now(),
            'login_count'   => $user->login_count + 1,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // audit log
        $this->auditLogService->log($user, 'login_success', null, [
            'last_login_at' => $user->last_login_at,
            'login_count'   => $user->login_count
        ]);

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user?->currentAccessToken()?->delete();
        $this->auditLogService->log($user, 'logout', null, null);

        return response()->json([
            'message' => 'Đăng xuất thành công',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('sellerProfile'),
        ]);
    }

    /**
     * Kiểm tra OTP cho email sinh viên @tdc.edu.vn
     * - tồn tại và còn hạn
     * - chưa dùng
     * - kiểm tra hash mã
     * - giới hạn attempts
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function assertEduOtpValid(string $email, ?string $otpCode): void
    {
        if (!$otpCode) {
            throw ValidationException::withMessages([
                'otp_code' => ['Vui lòng nhập mã xác thực.'],
            ]);
        }

        $otp = EmailOtp::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
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

        if (!Hash::check($otpCode, $otp->code_hash)) {
            $otp->increment('attempts');
            throw ValidationException::withMessages([
                'otp_code' => ['Mã không đúng.'],
            ]);
        }

        // Đánh dấu đã dùng để không tái sử dụng
        $otp->update(['used_at' => now()]);
    }
}
