<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TermsConsent;

class LegalController extends Controller
{
    public function terms()
    {
        return response()->json([
            'version' => config('terms.current_version', 'v1.0.0'),
            'format'  => 'static',
        ]);
    }

    public function consentStatus(Request $request)
    {
        $version = config('terms.current_version', 'v1.0.0');
        $user = $request->user();
        $consented = false;

        if ($user) {
            $consented = TermsConsent::where('user_id', $user->id)
                ->where('version', $version)
                ->exists();
        }

        return response()->json([
            'version'   => $version,
            'consented' => $consented,
        ]);
    }

    public function consent(Request $request)
    {
        $data = $request->validate([
            'version' => ['required','string','max:50'],
            'accept'  => ['required','boolean'],
        ]);

        if (!$data['accept']) {
            return response()->json(['message' => 'Bạn phải đồng ý điều khoản.'], 422);
        }

        $current = config('terms.current_version', 'v1.0.0');
        if ($data['version'] !== $current) {
            return response()->json(['message' => 'Sai phiên bản điều khoản.'], 422);
        }

        $consent = TermsConsent::firstOrCreate(
            ['user_id' => $request->user()->id, 'version' => $current],
            ['consented_at' => now(), 'ip_address' => $request->ip(), 'user_agent' => (string) $request->userAgent()]
        );

        return response()->json(['message' => 'Đã ghi nhận đồng ý.', 'consent' => $consent]);
    }
}
