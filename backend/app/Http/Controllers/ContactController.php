<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Log contact message for admin review
            Log::channel('single')->info('Contact Form Submission', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'timestamp' => now(),
            ]);

            // Optionally send email to admin
            // Mail::to(config('mail.admin_email'))->send(new ContactMail($validated));

            return response()->json([
                'message' => 'Your message has been sent successfully. We will get back to you soon!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to send message. Please try again later.'
            ], 500);
        }
    }
}
