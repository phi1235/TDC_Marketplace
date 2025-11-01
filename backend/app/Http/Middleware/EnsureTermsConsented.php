<?php

namespace App\Http\Middleware;

use App\Models\TermsConsent;
use Closure;
use Illuminate\Http\Request;

class EnsureTermsConsented
{
    public function handle(Request $request, Closure $next)
    {
        $version = config('terms.current_version');
        $ok = TermsConsent::where('user_id',$request->user()->id)->where('version',$version)->exists();
        if (!$ok) {
            return response()->json(['message' => 'Bạn cần đồng ý Điều khoản hiện hành.', 'required_version' => $version], 428);
        }
        return $next($request);
    }
}