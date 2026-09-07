<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class AuthController extends Controller
{
    public function customerRegister(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|max:30',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->first_name . ' ' . $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
    ]);

    $customerRole = Role::firstOrCreate([
        'name' => 'Customer'
    ]);

    $user->assignRole($customerRole);

    return response()->json([
        'status' => true,
        'message' => 'Account created successfully.',
        'redirect' => route('login')
    ]);
}
}
