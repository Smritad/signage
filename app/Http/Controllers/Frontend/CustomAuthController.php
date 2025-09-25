<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class CustomAuthController extends Controller
{
    // Show register page
    public function showRegister()
    {
        return view('frontend.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:custom_users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = CustomUser::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        Session::put('custom_user_id', $user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful!'
        ]);
    }

    // Show login page
    public function showLogin()
    {
        return view('frontend.login');
    }

    // Handle login
   public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

$user = CustomUser::where('email', trim($request->email))->first();
dd([
    'input_password' => $request->password,
    'db_password' => $user?->password,
    'hash_check' => $user ? Hash::check($request->password, $user->password) : null
]);

if (!$user || !Hash::check($request->password, $user->password)) {
    return response()->json([
        'status' => 'error',
        'message' => 'Invalid credentials'
    ]);
}

    Session::put('custom_user_id', $user->id);

    // Handle "remember me"
    if ($request->has('remember')) {
        // Set a cookie for 30 days
        cookie()->queue('remember_user', $user->id, 60*24*30);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Login successful!'
    ]);
}


    // Logout
    public function logout()
    {
        Session::forget('custom_user_id');
        return redirect()->route('frontend.index')->with('success', 'Logged out successfully!');
    }

    // Social login redirect
    public function redirectToSocial($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Social login callback
    public function handleSocialCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = CustomUser::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            ['password' => Hash::make(str()->random(12))]
        );

        Session::put('custom_user_id', $user->id);

        return "<script>alert('Login successful via $provider!'); window.location.href='".route('frontend.index')."';</script>";
    }
}
