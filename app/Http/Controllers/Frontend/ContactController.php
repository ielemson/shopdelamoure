<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Form
    |--------------------------------------------------------------------------
    */

    public function showRegistrationForm()
    {
        // Remove any values left by the previous arithmetic CAPTCHA.
        session()->forget([
            'captcha_question',
            'captcha_answer',
        ]);

        return view('auth.register');
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Validation
    |--------------------------------------------------------------------------
    */

    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'agree' => [
                'required',
                'accepted',
            ],

            'cf-turnstile-response' => [
                'required',
                'string',
                'max:2048',
            ],
        ], [
            'agree.required' => 'Please accept the terms to continue.',

            'agree.accepted' => 'Please accept the terms to continue.',

            'cf-turnstile-response.required' => 'Please complete the security verification.',

            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        $validator->after(function ($validator) use ($data) {
            // Verify Turnstile only after the other fields pass validation.
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $error = $this->verifyTurnstile(
                $data['cf-turnstile-response']
            );

            if ($error !== null) {
                $validator->errors()->add(
                    'cf-turnstile-response',
                    $error
                );
            }
        });

        return $validator;
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Verification
    |--------------------------------------------------------------------------
    */

    private function verifyTurnstile(string $token): ?string
    {
        $secretKey = config('services.turnstile.secret_key');

        if (empty($secretKey)) {
            return 'Security verification is unavailable. Please try again later.';
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(5)
                ->timeout(15)
                ->post(
                    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                    [
                        'secret' => $secretKey,
                        'response' => $token,
                    ]
                );
        } catch (ConnectionException $exception) {
            report($exception);

            return 'Unable to connect to security verification. Please try again.';
        }

        if (! $response->successful()) {
            return 'Security verification is temporarily unavailable. Please try again.';
        }

        $result = $response->json();

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
            data_get($result, 'action') !== 'register'
        ) {
            return 'Security verification failed or expired. Please verify again.';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Customer Account
    |--------------------------------------------------------------------------
    */

    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $firstName = trim($data['first_name']);
            $lastName = trim($data['last_name']);

            $user = User::create([
                'name' => $firstName.' '.$lastName,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            $user->customerProfile()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'country' => 'Nigeria',
                'status' => 'active',
                'customer_type' => 'regular',
            ]);

            $customerRole = Role::firstOrCreate([
                'name' => 'Customer',
                'guard_name' => $this->guard()->getName() === ''
                    ? config('auth.defaults.guard', 'web')
                    : config('auth.defaults.guard', 'web'),
            ]);

            $user->assignRole($customerRole);

            return $user;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Response
    |--------------------------------------------------------------------------
    */

    protected function registered(Request $request, $user)
    {
        $redirect = redirect()->intended(
            url($this->redirectPath())
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Account created successfully.',
                'redirect' => $redirect->getTargetUrl(),
            ]);
        }

        return $redirect;
    }
}
