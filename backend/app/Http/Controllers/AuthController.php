<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
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

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // audit log
        $this->auditLogService->log($user, 'user_created', null, $user->toArray());

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user,
            'token' => $token,
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

        

        // Tạm thời bỏ qua email verification để test
        // if (!$user->email_verified_at) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Vui lòng xác thực email trước khi đăng nhập.'],
        //     ]);
        // }
        if (!Auth::attempt($request->only('email', 'password'))) {
            // RateLimiter::hit($key, 600); // 10 phút
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        $user = Auth::user();
        
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản đã bị khóa, liên hệ quản trị viên.'],
            ]);
        }
        // RateLimiter::clear($key);
        
        $user->update([
            'last_login_at' => now(),
            'login_count' => $user->login_count + 1,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // audit log
        $this->auditLogService->log($user, 'login_success', null, ['last_login_at' => $user->last_login_at, 'login_count' => $user->login_count]);

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
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
}
