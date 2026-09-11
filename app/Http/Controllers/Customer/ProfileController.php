<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Profile Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        return view(
            'customer.profile.index',
            compact('user')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Personal Information
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'required',
                'email',
                'max:150',

                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $newEmail = strtolower(
            trim($validated['email'])
        );

        $emailChanged =
            $newEmail !== strtolower(
                (string) $user->email
            );

        $user->update([
            'name' => trim(
                $validated['name']
            ),

            'email' => $newEmail,

            'phone' => ! empty(
                $validated['phone']
            )
                ? trim($validated['phone'])
                : null,

            /*
            |--------------------------------------------------------------------------
            | Email Verification
            |--------------------------------------------------------------------------
            |
            | If the email address changes, it should be verified again.
            |
            */

            'email_verified_at' => $emailChanged
                    ? null
                    : $user->email_verified_at,
        ]);

        return back()->with(
            'success',
            'Your profile has been updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'confirmed',

                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ]);

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        return back()->with(
            'success',
            'Your password has been changed successfully.'
        );
    }
}
