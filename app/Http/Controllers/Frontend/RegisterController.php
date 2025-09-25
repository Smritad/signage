<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('frontend.register');
    }

   public function authenticateRegister(Request $request)
{
    // Validation
    $request->validate([
        'email' => 'required|email|unique:custom_users,email',
        'password' => 'required|confirmed|min:6',
    ], [
        'email.unique' => 'This email is already registered.',
        'password.confirmed' => 'Password confirmation does not match.',
    ]);

    // Create user
    $user = CustomUser::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'status' => 1, // active by default
        'remember_token' => \Str::random(60),
        'created_by' => null, // if admin creating, fill admin ID
    ]);

    // Redirect to login page with success message
    return redirect()->route('user.login')->with('message', 'Registration successful! Please login with your credentials.');
}
  

public function login()
{
    return view('frontend.login');
}



public function authenticateLogin(Request $request)
{
    // Validate input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');

    // Log login attempt
    Log::info('Login attempt', ['email' => $request->email, 'ip' => $request->ip(), 'time' => now()]);

    // Use 'custom' guard for custom_users
    if (Auth::guard('custom')->attempt(array_merge($credentials, ['status' => 1]), $request->has('remember'))) {
        $request->session()->regenerate();

        // Log successful login
        Log::info('Login successful', [
            'email' => Auth::guard('custom')->user()->email,
            'id' => Auth::guard('custom')->id(),
            'ip' => $request->ip(),
            'time' => now()
        ]);

        return redirect()->intended('/')->with('message', 'Welcome back, ' . Auth::guard('custom')->user()->email);
    }

    // Log failed login
    Log::warning('Login failed', ['email' => $request->email, 'ip' => $request->ip(), 'time' => now()]);

    return back()->withErrors([
        'email' => 'Invalid email or password.',
    ])->withInput();
}

public function logout(Request $request)
{
    // Use custom guard logout
    Auth::guard('custom')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('success', 'Logged out successfully.');
}

}
