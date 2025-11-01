<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_name' => 'required|string|max:100',
            'metadata' => 'nullable|array',
        ]);

        $activity = UserActivity::create([
            'user_id' => Auth::id(),
            'event_name' => $data['event_name'],
            'metadata' => $data['metadata'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['success' => true, 'id' => $activity->id]);
    }
}


