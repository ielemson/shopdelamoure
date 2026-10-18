<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use App\Models\WebsiteSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Contact Details
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|min:3|max:200',
            'message' => 'required|string|min:10|max:3000',
            'cf-turnstile-response' => 'required|string|max:2048',
        ], [
            'cf-turnstile-response.required' => 'Please complete the security verification.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Verify Cloudflare Turnstile
        |--------------------------------------------------------------------------
        */

        $secretKey = config('services.turnstile.secret_key');

        if (empty($secretKey)) {
            return response()->json([
                'status' => false,
                'message' => 'Security verification is unavailable. Please try again later.',
            ], 503);
        }

        try {
            $verification = Http::asForm()
                ->connectTimeout(5)
                ->timeout(15)
                ->post(
                    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                    [
                        'secret' => $secretKey,
                        'response' => $validated['cf-turnstile-response'],
                    ]
                );
        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json([
                'status' => false,
                'message' => 'Unable to connect to security verification. Please try again.',
            ], 503);
        }

        if (! $verification->successful()) {
            return response()->json([
                'status' => false,
                'message' => 'Security verification is temporarily unavailable. Please try again.',
            ], 503);
        }

        $result = $verification->json();

        $allowedHostnames = [
            'shopdelamoure.com',
            'www.shopdelamoure.com',
        ];

        if (
            data_get($result, 'success') !== true ||
            ! in_array(
                data_get($result, 'hostname'),
                $allowedHostnames,
                true
            ) ||
            data_get($result, 'action') !== 'contact'
        ) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Security verification failed or expired. Please verify again.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get Recipient
        |--------------------------------------------------------------------------
        */

        $setting = WebsiteSetting::first();

        $toEmail = $setting?->email ?: config('mail.from.address');

        if (empty($toEmail)) {
            return response()->json([
                'status' => false,
                'message' => 'We cannot receive messages right now. Please try again later.',
            ], 503);
        }

        /*
        |--------------------------------------------------------------------------
        | Send Contact Email
        |--------------------------------------------------------------------------
        */

        Mail::to($toEmail)->send(
            new ContactUsMail(
                name: $validated['name'],
                email: $validated['email'],
                subjectText: $validated['subject'],
                contactMessage: $validated['message']
            )
        );

        return response()->json([
            'status' => true,
            'message' => 'Thank you. Your message has been sent successfully.',
        ]);
    }
}
