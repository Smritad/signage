<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use App\Models\ProductsDetails;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomResetPasswordMail;
use Carbon\Carbon;
use App\Traits\MergesGuestCart;

class RegisterController extends Controller
{
        use MergesGuestCart;   // ★ add this line

    public function showRegister()
    {
        return view('frontend.register');
    }

    public function globalSearch(Request $request)
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        $products = ProductsDetails::where('product_name', 'like', "%{$q}%")
            ->with(['category:id,slug', 'subCategory:id,slug'])
            ->limit(8)
            ->get()
            ->map(function($p) {
                $catSlug = optional($p->category)->slug ?? 'category';
                $subSlug = optional($p->subCategory)->slug ?? 'subcategory';
                return [
                    'type'  => 'product',
                    'title' => $p->product_name,
                    'url'   => route('product.details', [$catSlug, $subSlug, $p->slug]),
                ];
            });

        $categories = CategoryDetails::where('category_name', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(function($c) {
                $hasProducts = ProductsDetails::where('category_id', $c->id)->exists();
                return [
                    'type'  => 'category',
                    'title' => $c->category_name,
                    'url'   => $hasProducts ? route('product.category', $c->slug) : route('coming.soon'),
                ];
            });

        $subcats = SabCategoryDetails::where('sab_category_name', 'like', "%{$q}%")
            ->with('category:id,slug')
            ->limit(5)
            ->get()
            ->map(function($s) {
                $masterSlug  = optional($s->category)->slug ?? 'category';
                $hasProducts = ProductsDetails::where('sub_category_id', $s->id)->exists();
                return [
                    'type'  => 'subcategory',
                    'title' => $s->sab_category_name,
                    'url'   => $hasProducts
                        ? route('product.subcategory', [$masterSlug, $s->slug])
                        : route('coming.soon'),
                ];
            });

        return response()->json(
            collect($categories)->merge($subcats)->merge($products)->values()
        );
    }

    public function authenticateRegister(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|unique:custom_users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%&*!])[A-Za-z\d@#$%&*!]{8,}$/',
            ],
        ], [
            'email.unique'       => 'This email is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.regex'     => 'Password must include uppercase, lowercase, a number, and a special character (@ # $ % & * !).',
        ]);

        CustomUser::create([
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'status'         => 1,
            'remember_token' => Str::random(60),
            'created_by'     => null,
        ]);

        return redirect()->route('user.login')
                         ->with('message', 'Registration successful! Please login with your credentials.');
    }

    public function login()
    {
        return view('frontend.login');
    }

    public function forgotpassword()
    {
        return view('frontend.forgotpassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        $user = CustomUser::where('email', $request->email)->first();
        if (!$user) {
            return back()
                ->with('error', 'No account found with this email address.')
                ->withInput();
        }

        // ✅ Check if a reset link was already sent within 10 minutes
        $existing = DB::table('password_resets')
            ->where('email', $user->email)
            ->first();

        if ($existing) {
            $sentAt      = Carbon::parse($existing->created_at);
            $elapsedMins = $sentAt->diffInMinutes(now());

            if ($elapsedMins < 10) {
                $minutesLeft = 10 - $elapsedMins;
                return back()
                    ->with('error', 'Reset password link is already sent to your email. Please check your inbox. You can try again after ' . $minutesLeft . ' minute(s).')
                    ->withInput();
            }
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email'      => $user->email,
                'token'      => bcrypt($token),
                'created_at' => now(),
            ]
        );

        Mail::to($user->email)->send(new CustomResetPasswordMail($token, $user->email));

        return back()->with('message', 'Password reset link has been sent to your email! It is valid for 10 minutes.');
    }

    public function resetpassword($token)
    {
        return view('frontend.resetpassword', ['token' => $token]);
    }

   public function updatePassword(Request $request)
{
    $request->validate([
        'email'    => 'required|email|exists:custom_users,email',
        'password' => [
            'required',
            'confirmed',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%&*!])[A-Za-z\d@#$%&*!]{8,}$/',
        ],
        'token' => 'required',
    ], [
        'email.exists'   => 'This email is not registered. Please enter the correct email.',
        'email.required' => 'Please enter your email address.',
        'email.email'    => 'Please enter a valid email address.',
        'password.min'   => 'Password must be at least 8 characters.',
        'password.regex' => 'Password must include uppercase, lowercase, a number, and a special character (@ # $ % & * !).',
    ]);

    // ✅ Check token exists and has NOT expired (10 minutes)
    $resetRecord = DB::table('password_resets')
        ->where('email', $request->email)
        ->first();

    if (!$resetRecord) {
        return back()
            ->with('error_type', 'email')
            ->with('error', 'Invalid password reset request. Please request a new link.')
            ->withInput();
    }

    $sentAt = Carbon::parse($resetRecord->created_at);
    if ($sentAt->diffInMinutes(now()) > 10) {
        DB::table('password_resets')->where('email', $request->email)->delete();
        return back()
            ->with('error_type', 'email')
            ->with('error', 'This reset link has expired. Please request a new one.')
            ->withInput();
    }

    if (!Hash::check($request->token, $resetRecord->token)) {
        return back()
            ->with('error_type', 'email')
            ->with('error', 'Invalid reset link. Please request a new one.')
            ->withInput();
    }

    $user = CustomUser::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('user.login')
                     ->with('message', 'Password reset successfully! Please login with your new password.');
}
   public function authenticateLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', [
            'email' => $request->email,
            'ip'    => $request->ip(),
            'time'  => now()
        ]);

        // ✅ Step 1: Check if email exists
        $user = CustomUser::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->with('error_type', 'email')
                ->with('error', 'Wrong email entered.')
                ->withInput();
        }

        // ✅ Step 2: Check if account is active
        if ($user->status != 1) {
            return back()
                ->with('error_type', 'email')
                ->with('error', 'Your account is inactive. Please contact support.')
                ->withInput();
        }

        // ✅ Step 3: Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->with('error_type', 'password')
                ->with('error', 'Wrong password entered.')
                ->withInput();
        }

        // ★ Capture the guest session id BEFORE login/regenerate, or the
        //   guest cart rows (keyed by this session_id) get orphaned.
        $guestSessionId = session()->getId();

        // ✅ Step 4: Login
        Auth::guard('custom')->login($user, $request->has('remember'));
        $request->session()->regenerate();

        Log::info('Login successful', [
            'email' => $user->email,
            'id'    => $user->id,
            'ip'    => $request->ip(),
            'time'  => now()
        ]);

        // ★ Merge guest cart (carts table, by session_id) into this user's cart.
        $this->mergeGuestCart($user->id, $guestSessionId);

        if ($request->input('from_checkout') == '1') {
            return redirect()->route('show.checkout')
                             ->with('message', 'Welcome back! Proceed to checkout.');
        }

       return redirect()->intended('/')
    ->with(
        'message',
        'Welcome, ' . (
            !empty($user->name)
                ? ucwords($user->name)
                : explode('@', $user->email)[0]
        )
    );
    }

    public function logout(Request $request)
    {
        Auth::guard('custom')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/user-login')->with('message', 'Logged out successfully.');
    }
}