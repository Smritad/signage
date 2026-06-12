<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\ProductsDetails;

class WishlistController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    // Display wishlist
    // ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $userId    = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
        $sessionId = $userId ? null : session()->getId();

        // Clean up expired guest wishlist items
        Wishlist::whereNotNull('session_id')
            ->where('created_at', '<', now()->subDay())
            ->delete();

        $wishlist = Wishlist::when($userId,  fn($q) => $q->where('user_id',    $userId))
                            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                            ->with('product')
                            ->get()
                            ->filter(fn($item) => $item->product);

        return view('frontend.wishlist', compact('wishlist'));
    }

    // ─────────────────────────────────────────────────────────────────
    // Add / remove item (toggle)
    // FIX: use $product->first_sub_category_id (plain integer) instead
    //      of $product->sub_category_id (may be a JSON array string like
    //      '["2","3"]' which MySQL rejects on an INT column)
    // ─────────────────────────────────────────────────────────────────
    public function add(Request $request)
    {
        $userId    = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
        $sessionId = $userId ? null : session()->getId();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('product_id', $productId)
                            ->when($userId,  fn($q) => $q->where('user_id',    $userId))
                            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $status  = 'removed';
            $message = 'Product removed from wishlist!';
        } else {
            $product = ProductsDetails::findOrFail($productId);

            Wishlist::create([
                'user_id'         => $userId,
                'session_id'      => $sessionId,
                'category_id'     => $product->category_id,
                'sub_category_id' => $product->first_sub_category_id, // ✅ plain integer
                'product_id'      => $product->id,
                'product_name'    => $product->product_name,
                'slug'            => $product->slug,
                'price'           => $product->price,
                'offer_price'     => $product->offer_price,
                'images'          => $product->images,
            ]);

            $status  = 'added';
            $message = 'Product added to wishlist!';
        }

        $count = $userId
            ? Wishlist::where('user_id',    $userId)->count()
            : Wishlist::where('session_id', $sessionId)->count();

        return response()->json([
            'status'  => $status,
            'message' => $message,
            'count'   => $count,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Remove single item (from wishlist page)
    // ─────────────────────────────────────────────────────────────────
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

    // ─────────────────────────────────────────────────────────────────
    // Bulk delete
    // ─────────────────────────────────────────────────────────────────
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            Wishlist::whereIn('id', $ids)->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}