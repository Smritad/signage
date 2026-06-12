<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomUser;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MyAccountController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:custom'),
        ];
    }

    private function authUser()
    {
        return Auth::guard('custom')->user();
    }

    /* ══════════════════════════════════════════════════════════
     |  DASHBOARD
     ══════════════════════════════════════════════════════════ */
    public function index()
    {
        $user = $this->authUser();

        $recentOrders = OrderDetail::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $this->decodeOrderJson($recentOrders);

        $totalOrders      = OrderDetail::where('user_id', $user->id)->count();
        $successfulOrders = OrderDetail::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'cod'])
            ->count();
        $pendingOrders = OrderDetail::where('user_id', $user->id)
            ->whereIn('payment_status', ['pending', 'failed', 'expired', 'cancelled'])
            ->count();

        return view('frontend.myaccount', compact(
            'user', 'recentOrders', 'totalOrders', 'successfulOrders', 'pendingOrders'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  ALL ORDERS
     ══════════════════════════════════════════════════════════ */
    public function orderdetails()
    {
        $user = $this->authUser();

        $recentOrders = OrderDetail::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(8)
            ->through(function ($order) {
                $this->decodeSingleOrderJson($order);
                return $order;
            });

        return view('frontend.orderdetails', compact('user', 'recentOrders'));
    }

    /* ══════════════════════════════════════════════════════════
     |  SINGLE ORDER DETAILS
     ══════════════════════════════════════════════════════════ */
    public function orderdetailsview($id)
    {
        $user = $this->authUser();

        $order = OrderDetail::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $this->decodeSingleOrderJson($order);

        $items = [];

        foreach ($order->product_names as $i => $name) {

            $offerId   = (int)   ($order->offer_ids[$i]  ?? 0);
            $offerData =          $order->offer_data[$i] ?? [];
            $rawImage  =          $order->images[$i]     ?? null;
            $price     = (float) ($order->prices[$i]     ?? 0);
            $qty       = (int)   ($order->quantities[$i] ?? 1);
            $subtotal  = (float) ($order->subtotals[$i]  ?? ($price * $qty));

            if ($offerId > 0) {
                $finalPrice = (float) ($offerData['final_price'] ?? $price);
                $mrpTotal   = (float) ($offerData['mrp_total']   ?? $finalPrice);
                $offerImg   = $offerData['offer_image'] ?? $rawImage ?? '';

                if (str_starts_with((string) $offerImg, 'http')) {
                    $offerImg = basename(parse_url($offerImg, PHP_URL_PATH));
                }
                $offerImg = trim((string) $offerImg, '"\'');

                $imageUrl = !empty($offerImg)
                    ? asset('offerimage/' . $offerImg)
                    : asset('images/no-image.png');

                $items[] = [
                    'isOffer'       => true,
                    'name'          => $offerData['offer_name'] ?? $name,
                    'image'         => $imageUrl,
                    'price'         => $finalPrice,
                    'mrp'           => $mrpTotal,
                    'qty'           => 1,
                    'subtotal'      => $finalPrice,
                    'selectedItems' => $offerData['selected'] ?? [],
                ];

            } else {
                if (is_string($rawImage) && str_starts_with(trim($rawImage), '[')) {
                    $dec      = json_decode($rawImage, true);
                    $rawImage = is_array($dec) ? ($dec[0] ?? null) : $rawImage;
                }
                $rawImage = trim((string) ($rawImage ?? ''), '"\'');

                if (empty($rawImage)) {
                    $imageUrl = asset('images/no-image.png');
                } elseif (str_starts_with($rawImage, 'http')) {
                    $imageUrl = $rawImage;
                } else {
                    $imageUrl = asset('signage/home/productimage/' . basename($rawImage));
                }

                $items[] = [
                    'isOffer'       => false,
                    'name'          => $name,
                    'image'         => $imageUrl,
                    'price'         => $price,
                    'mrp'           => $price,
                    'qty'           => $qty,
                    'subtotal'      => $subtotal,
                    'selectedItems' => [],
                ];
            }
        }

        return view('frontend.orderdetailsview', compact('user', 'order', 'items'));
    }

    /* ══════════════════════════════════════════════════════════
     |  HELPERS
     ══════════════════════════════════════════════════════════ */
    private function decodeOrderJson($orders)
    {
        $orders->transform(function ($order) {
            $this->decodeSingleOrderJson($order);
            return $order;
        });
    }

    private function decodeSingleOrderJson($order)
    {
        $order->product_ids   = json_decode($order->product_ids   ?? '[]', true) ?? [];
        $order->product_names = json_decode($order->product_names ?? '[]', true) ?? [];
        $order->quantities    = json_decode($order->quantities     ?? '[]', true) ?? [];
        $order->prices        = json_decode($order->prices        ?? '[]', true) ?? [];
        $order->subtotals     = json_decode($order->subtotals     ?? '[]', true) ?? [];
        $order->images        = json_decode($order->images        ?? '[]', true) ?? [];
        $order->sizes         = json_decode($order->sizes         ?? '[]', true) ?? [];
        $order->offer_ids     = json_decode($order->offer_ids     ?? '[]', true) ?? [];
        $order->offer_data    = json_decode($order->offer_data    ?? '[]', true) ?? [];
    }

    /* ══════════════════════════════════════════════════════════
     |  ADDRESS
     ══════════════════════════════════════════════════════════ */
    public function address()
    {
        $user            = $this->authUser();
        $billingAddress  = $user->billing_address  ?? null;
        $shippingAddress = $user->shipping_address ?? null;

        return view('frontend.address', compact('user', 'billingAddress', 'shippingAddress'));
    }

    public function updateAddress(Request $request, $type)
    {
        $user = $this->authUser();

        $request->validate(['address' => 'required|string|max:500']);

        $fieldMap = [
            'billing'  => 'billing_address',
            'shipping' => 'shipping_address',
        ];
        $type = $fieldMap[$type] ?? $type;

        if (in_array($type, $user->getFillable())) {
            $user->$type = $request->address;
            $user->save();

            $message = ucfirst(str_replace('_', ' ', $type)) . ' updated successfully!';

            if ($request->ajax()) return response()->json(['success' => $message]);

            return redirect()->route('frontend.address')->with('success', $message);
        }

        abort(403, 'Invalid field');
    }

    /* ══════════════════════════════════════════════════════════
     |  ACCOUNT SETTINGS
     ══════════════════════════════════════════════════════════ */
    public function accountsetting()
    {
        $user = $this->authUser();
        return view('frontend.accountsetting', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        $user = $this->authUser();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:custom_users,email,' . $user->id,
        ], [
            'name.required'  => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique'   => 'This email is already taken by another account.',
        ]);

        // ✅ FIX: trim values so browser-autofilled / whitespace-only fields
        // don't falsely trigger the password-change branch.
        $changingPassword = trim((string) $request->input('current_password')) !== ''
                         || trim((string) $request->input('new_password')) !== ''
                         || trim((string) $request->input('new_password_confirmation')) !== '';

        if ($changingPassword) {
            $request->validate([
                'current_password'          => 'required|string',
                'new_password'              => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%&*!])[A-Za-z\d@#$%&*!]{8,}$/',
                ],
                'new_password_confirmation' => 'required',
            ], [
                'current_password.required'          => 'Please enter your current password.',
                'new_password.required'              => 'Please enter a new password.',
                'new_password.min'                   => 'New password must be at least 8 characters.',
                'new_password.regex'                 => 'Password must include uppercase, lowercase, a number, and a special character (@ # $ % & * !).',
                'new_password.confirmed'             => 'New password confirmation does not match.',
                'new_password_confirmation.required' => 'Please confirm your new password.',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->with('error_type', 'current_password')
                    ->with('error', 'Current password is incorrect. Please try again.')
                    ->withInput();
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success',
            $changingPassword ? 'Password updated successfully!' : 'Account updated successfully!'
        );
    }

    public function updateProfileImage(Request $request)
    {
        $user = $this->authUser();

        $request->validate(['avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048']);

        if ($request->hasFile('avatar')) {
            $file     = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('signage/home/productimage'), $filename);

            $user->avatar = $filename;
            $user->save();

            return response()->json(['success' => 'Profile image updated successfully!']);
        }

        return response()->json(['error' => 'No image selected.']);
    }
}