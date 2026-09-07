<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    // public function send(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'    => 'required|string|max:100',
    //         'email'   => 'required|email|max:150',
    //         'subject' => 'required|string|max:200',
    //         'message' => 'required|string|min:10|max:3000',
    //     ]);

    //     $setting = WebsiteSetting::first();

    //     $toEmail = $setting?->email ?? config('mail.from.address');

    //     Mail::send('emails.contact-message', $validated, function ($message) use ($validated, $toEmail, $setting) {
    //         $message->to($toEmail)
    //             ->subject('New Contact Message - ' . $validated['subject'])
    //             ->replyTo($validated['email'], $validated['name']);
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Thank you. Your message has been sent successfully.',
    //     ]);
    // }


    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:3000',
        ]);

        $setting = WebsiteSetting::first();

        $toEmail = $setting?->email ?? config('mail.from.address');

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
