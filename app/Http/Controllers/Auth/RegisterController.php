<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function showRegistrationForm()
    {
        $a = rand(2, 9);
        $b = rand(2, 9);

        session([
            'captcha_question' => "$a + $b = ?",
            'captcha_answer' => $a + $b,
        ]);

        return view('auth.register');
    }

   protected function validator(array $data)
{
    return Validator::make($data, [
        'first_name' => ['required', 'string', 'max:100'],
        'last_name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],

        'phone' => [
            'required',
            'regex:/^[0-9+\-\s()]+$/',
            'max:20'
        ],

        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'captcha' => ['required', 'numeric'],
    ]);
}


  protected function create(array $data)
{
    if ((int) $data['captcha'] !== (int) session('captcha_answer')) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'captcha' => ['Invalid captcha answer. Please try again.']
        ]);
    }

    $user = User::create([
        'name' => $data['first_name'] . ' ' . $data['last_name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'password' => Hash::make($data['password']),
    ]);

    $user->customerProfile()->create([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'country' => 'Nigeria',
        'status' => 'active',
        'customer_type' => 'regular',
    ]);

    $customerRole = Role::firstOrCreate([
        'name' => 'Customer'
    ]);

    $user->assignRole($customerRole);

    return $user;
}

    // protected function registered(Request $request, $user)
    // {
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Account created successfully.',
    //             'redirect' => route('login')
    //         ]);
    //     }

    //     return redirect($this->redirectPath());
    // }
    protected function registered(Request $request, $user)
{
    $redirectUrl = redirect()->intended(url($this->redirectTo))->getTargetUrl();

    if ($request->ajax()) {
        return response()->json([
            'status' => true,
            'message' => 'Account created successfully.',
            'redirect' => $redirectUrl,
        ]);
    }

    return redirect()->intended(url($this->redirectTo));
}

}