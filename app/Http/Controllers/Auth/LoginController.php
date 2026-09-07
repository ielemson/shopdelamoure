<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// class LoginController extends Controller
// {
//     use AuthenticatesUsers;

//     /**
//      * Where to redirect users after login.
//      *
//      * @var string
//      */
//     protected $redirectTo = '/home';

//     /**
//      * Create a new controller instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         $this->middleware('guest')->except('logout');
//         $this->middleware('auth')->only('logout');
//     }

//     /**
//      * AJAX success response
//      */
//     protected function authenticated(Request $request, $user)
//     {
//         if ($request->ajax()) {

//             return response()->json([
//                 'status' => true,
//                 'message' => 'Login successful.',
//                 'redirect' => url($this->redirectTo),
//             ]);

//         }
//     }

//     /**
//      * AJAX failed response
//      */
//     protected function sendFailedLoginResponse(Request $request)
//     {
//         if ($request->ajax()) {

//             throw ValidationException::withMessages([
//                 $this->username() => ['Invalid email or password.'],
//             ]);

//         }

//         throw ValidationException::withMessages([
//             $this->username() => [trans('auth.failed')],
//         ]);
//     }
// }

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $redirectUrl = redirect()->intended(url($this->redirectTo))->getTargetUrl();

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended(url($this->redirectTo));
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->ajax()) {
            throw ValidationException::withMessages([
                $this->username() => ['Invalid email or password.'],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}