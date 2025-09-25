<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductsDetails;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Show cart page
    public function index()
{
    // Auto-cleanup expired guest carts (older than 24 hours)
    Cart::whereNotNull('session_id')
        ->where('created_at', '<', now()->subDay())
        ->delete();

    $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : session()->getId();

    $carts = Cart::when(Auth::guard('custom')->check(), fn($q) => $q->where('user_id', $userId))
                 ->when(!Auth::guard('custom')->check(), fn($q) => $q->where('session_id', $userId))
                 ->get();

    return view('frontend.cart', compact('carts'));
}

public function updateQuantity(Request $request)
{
    $request->validate([
        'cart_id' => 'required|exists:carts,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // Find and update cart
    $cart = Cart::findOrFail($request->cart_id);
    $cart->quantity = $request->quantity;
    $cart->save();

    // Calculate cart count
    $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
    $sessionId = $userId ? null : session()->getId();

    $cart_count = Cart::when($userId, fn($q) => $q->where('user_id', $userId))
                      ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                      ->sum('quantity');

    return response()->json([
        'success' => true,
        'message' => 'Quantity updated successfully!',
        'cart_count' => $cart_count
    ]);
}

    // Add product to cart
    public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products_details,id',
        'quantity'   => 'nullable|integer|min:1'
    ]);

    $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
    $sessionId = $userId ? null : session()->getId();

    $product = ProductsDetails::findOrFail($request->product_id);

    // Check if product already in cart
    $cartQuery = Cart::where('product_id', $product->id);
    if ($userId) {
        $cartQuery->where('user_id', $userId);
    } else {
        $cartQuery->where('session_id', $sessionId);
    }

    $cart = $cartQuery->first();

    $quantity = $request->quantity ?? 1;

    if ($cart) {
        $cart->increment('quantity', $quantity);
        $message = 'Product quantity updated in cart!';
    } else {
        Cart::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'slug' => $product->slug,
            'price' => $product->price,
            'offer_price' => $product->offer_price,
            'quantity' => $quantity,
            'images' => $product->images,
        ]);
        $message = 'Product added to cart!';
    }

    // Count **distinct products** in cart (not sum of quantities)
    $cart_count = Cart::when($userId, fn($q) => $q->where('user_id', $userId))
                      ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                      ->count(); // ✅ count rows = distinct products

    return response()->json([
        'success' => true,
        'message' => $message,
        'cart_count' => $cart_count
    ]);
}

    // Remove product from cart
   public function remove(Request $request)
{
    $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : session()->getId();

    Cart::when(Auth::guard('custom')->check(), fn($q) => $q->where('user_id', $userId))
        ->when(!Auth::guard('custom')->check(), fn($q) => $q->where('session_id', $userId))
        ->where('id', $request->cart_id)
        ->delete();

    $cartCount = Cart::when(Auth::guard('custom')->check(), fn($q) => $q->where('user_id', $userId))
                     ->when(!Auth::guard('custom')->check(), fn($q) => $q->where('session_id', $userId))
                     ->count();

    return response()->json([
        'success' => true,
        'message' => 'Product removed from cart!',
        'cart_count' => $cartCount
    ]);
}



}
