<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\ProductsDetails;

class WishlistController extends Controller
{
    // Display wishlist
    public function index()
{
    $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
    $sessionId = $userId ? null : session()->getId();

    // Auto-cleanup expired guest wishlist items (older than 24 hours)
    Wishlist::whereNotNull('session_id')
        ->where('created_at', '<', now()->subDay())
        ->delete();

    $wishlist = Wishlist::when($userId, fn($q) => $q->where('user_id', $userId))
                        ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                        ->get();

    return view('frontend.wishlist', compact('wishlist'));
}

    // Add/remove item to wishlist
    public function add(Request $request)
    {
        $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
        $sessionId = $userId ? null : session()->getId();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('product_id', $productId)
                        ->when($userId, fn($q) => $q->where('user_id', $userId))
                        ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                        ->first();

        if ($wishlist) {
            $wishlist->delete();
            $status = 'removed';
            $message = 'Product removed from wishlist!';
        } else {
            $product = ProductsDetails::findOrFail($productId);
            Wishlist::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'slug' => $product->slug,
                'price' => $product->price,
                'offer_price' => $product->offer_price,
                'images' => $product->images,
            ]);
            $status = 'added';
            $message = 'Product added to wishlist!';
        }

        $count = $userId
            ? Wishlist::where('user_id', $userId)->count()
            : Wishlist::where('session_id', $sessionId)->count();

        return response()->json([
            'status' => $status,
            'message' => $message,
            'count' => $count
        ]);
    }

  


   public function remove($id)
{
    if (Auth::guard('custom')->check()) {
        $wishlist = Wishlist::where('id', $id)
            ->where('user_id', Auth::guard('custom')->id())
            ->first();
    } else {
        $wishlist = Wishlist::where('id', $id)
            ->where('session_id', session()->getId())
            ->first();
    }

    if ($wishlist) {
        $wishlist->delete();

        return redirect()->route('wishlist.index')
                         ->with('message', 'Item removed from your wishlist!');
    }

    return redirect()->route('wishlist.index')
                     ->with('error', 'Item not found in your wishlist.');
}



}
