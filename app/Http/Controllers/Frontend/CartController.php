<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductsDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function userSession(): array
    {
        $userId    = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
        $sessionId = $userId ? null : session()->getId();
        return [$userId, $sessionId];
    }

    private function forUser($query, $userId, $sessionId)
    {
        return $query->when($userId,  fn($q) => $q->where('user_id',    $userId))
                     ->when(!$userId, fn($q) => $q->where('session_id', $sessionId));
    }

    public function index()
    {
        Cart::whereNotNull('session_id')->where('created_at', '<', now()->subDay())->delete();
        [$userId, $sessionId] = $this->userSession();
        $carts = $this->forUser(Cart::query(), $userId, $sessionId)->get();
        return view('frontend.cart', compact('carts'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|exists:products_details,id',
            'quantity'         => 'nullable|integer|min:1',
            'from_wishlist'    => 'nullable|boolean',
            'wishlist_item_id' => 'nullable|integer',
        ]);

        [$userId, $sessionId] = $this->userSession();
        $product       = ProductsDetails::findOrFail($request->product_id);
        $quantity      = $request->quantity ?? 1;
        $subCategoryId = $product->first_sub_category_id;

        $cart = $this->forUser(
            Cart::where('product_id', $product->id)->where('combo', 'no'),
            $userId, $sessionId
        )->first();

        if ($cart) {
            $cart->increment('quantity', $quantity);
            $message = 'Product quantity updated in cart!';
        } else {
            // ★ DO NOT pass images through Eloquent if Cart model casts images to array.
            //   Use DB::table() to bypass any casting.
            DB::table('carts')->insert([
                'user_id'         => $userId,
                'session_id'      => $sessionId,
                'category_id'     => $product->category_id,
                'sub_category_id' => $subCategoryId,
                'product_id'      => $product->id,
                'offer_id'        => null,
                'product_name'    => $product->product_name,
                'slug'            => $product->slug,
                'price'           => $product->price,
                'offer_price'     => $product->offer_price,
                'quantity'        => $quantity,
                'images'          => $product->images, // plain JSON string as stored in products_details
                'combo'           => 'no',
                'combo_text'      => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $message = 'Product added to cart!';
        }

        $wishlistRemoved = false;
        if ($request->from_wishlist) {
            $wishlistQuery = \App\Models\Wishlist::where('product_id', $product->id);
            if ($request->filled('wishlist_item_id')) {
                $wishlistQuery->where('id', $request->wishlist_item_id);
            }
            $wishlistQuery = $userId
                ? $wishlistQuery->where('user_id', $userId)
                : $wishlistQuery->where('session_id', $sessionId);
            $wishlistRemoved = (bool) $wishlistQuery->delete();
            if ($wishlistRemoved) $message = 'Product moved to cart!';
        }

        $cart_count = $this->forUser(Cart::query(), $userId, $sessionId)->count();
        $wishlist_count = $userId
            ? \App\Models\Wishlist::where('user_id', $userId)->count()
            : \App\Models\Wishlist::where('session_id', $sessionId)->count();

        return response()->json([
            'success'          => true,
            'message'          => $message,
            'cart_count'       => $cart_count,
            'wishlist_count'   => $wishlist_count,
            'wishlist_removed' => $wishlistRemoved,
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate(['cart_id' => 'required|exists:carts,id', 'quantity' => 'required|integer|min:1']);
        $cart = Cart::findOrFail($request->cart_id);
        if ($cart->combo === 'offer') {
            return response()->json(['success' => false, 'message' => 'Bundle quantity cannot be changed.']);
        }
        $cart->quantity = $request->quantity;
        $cart->save();
        [$userId, $sessionId] = $this->userSession();
        $cart_count = $this->forUser(Cart::query(), $userId, $sessionId)->sum('quantity');
        return response()->json(['success' => true, 'message' => 'Quantity updated!', 'cart_count' => $cart_count]);
    }

    public function bulkAddToCart(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:1']);
        [$userId, $sessionId] = $this->userSession();

        $wishlistItems = \App\Models\Wishlist::whereIn('id', $request->ids)
            ->when($userId, fn($q) => $q->where('user_id', $userId))->get();

        if ($wishlistItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid wishlist items found.']);
        }

        foreach ($wishlistItems as $item) {
            $product = ProductsDetails::find($item->product_id);
            if (!$product) continue;
            $subCategoryId = $product->first_sub_category_id;
            $cart = $this->forUser(
                Cart::where('product_id', $product->id)->where('combo', 'no'), $userId, $sessionId
            )->first();
            if ($cart) {
                $cart->increment('quantity', 1);
            } else {
                DB::table('carts')->insert([
                    'user_id' => $userId, 'session_id' => $sessionId,
                    'category_id' => $product->category_id, 'sub_category_id' => $subCategoryId,
                    'product_id' => $product->id, 'offer_id' => null,
                    'product_name' => $product->product_name, 'slug' => $product->slug,
                    'price' => $product->price, 'offer_price' => $product->offer_price,
                    'quantity' => 1, 'images' => $product->images,
                    'combo' => 'no', 'combo_text' => null,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        \App\Models\Wishlist::whereIn('id', $request->ids)
            ->when($userId, fn($q) => $q->where('user_id', $userId))->delete();

        $cart_count = $this->forUser(Cart::query(), $userId, $sessionId)->count();
        return response()->json(['success' => true, 'message' => 'Items moved to cart!', 'cart_count' => $cart_count]);
    }

    public function remove(Request $request)
    {
        [$userId, $sessionId] = $this->userSession();
        $this->forUser(Cart::where('id', $request->cart_id), $userId, $sessionId)->delete();
        $cartCount = $this->forUser(Cart::query(), $userId, $sessionId)->count();
        return response()->json(['success' => true, 'message' => 'Removed!', 'cart_count' => $cartCount]);
    }

    /* ─────────────────────────────────────────────────────────────
     | ★ Add Bundle (Offer) to Cart
     |
     |  STORAGE RULES (must match how cart blade reads it):
     |  images     → plain filename string  e.g. "1778739765_offer.png"
     |  offer_id   → plain integer          e.g. 4
     |  combo      → 'offer'
     |  combo_text → JSON array of selected products
     |  price      → MRP total (for strikethrough)
     |  offer_price→ final discounted price
     ───────────────────────────────────────────────────────────── */
    public function addBundle(Request $request)
    {
        $offerId  = (int) $request->input('offer_id', 0);
        $products = json_decode($request->input('products', '{}'), true) ?? [];

        if (!$offerId || empty($products)) {
            return response()->json(['success' => false, 'message' => 'Invalid bundle data.']);
        }

        $offer = DB::table('offers')
            ->where('id', $offerId)->where('is_active', 1)->whereNull('deleted_at')->first();

        if (!$offer) {
            return response()->json(['success' => false, 'message' => 'Offer not found or inactive.']);
        }

        /* Flatten selected products */
        $selectedFlat = [];
        foreach ($products as $stepNo => $stepItems) {
            foreach ((array) $stepItems as $item) {
                $selectedFlat[] = [
                    'step'  => (int)    $stepNo,
                    'id'    => (int)    ($item['id']    ?? 0),
                    'name'  => (string) ($item['name']  ?? ''),
                    'unit'  => (string) ($item['unit']  ?? ''),
                    'image' => (string) ($item['image'] ?? ''),
                    'price' => (float)  ($item['price'] ?? 0),
                ];
            }
        }

        $mrpTotal = array_sum(array_column($selectedFlat, 'price'));

        $offerPriceType = $offer->offer_price_type ?? 'fixed';
        $offerPriceVal  = (float) ($offer->offer_price ?? 0);

        if ($offerPriceType === 'fixed') {
            $finalPrice = $offerPriceVal;
        } elseif ($offerPriceType === 'percent') {
            $finalPrice = round($mrpTotal * (1 - $offerPriceVal / 100), 2);
        } else {
            $finalPrice = $mrpTotal;
        }

        // ★ Plain filename — no json_encode — so blade can use it directly
        $offerImageFile = trim($offer->offer_image ?? '');

        $userId    = Auth::guard('custom')->id();
        $sessionId = session()->getId();

        $existingQuery = DB::table('carts')->where('offer_id', $offerId)->where('combo', 'offer');
        $userId ? $existingQuery->where('user_id', $userId) : $existingQuery->where('session_id', $sessionId);
        $existing = $existingQuery->first();
        $now      = now();

        if ($existing) {
            DB::table('carts')->where('id', $existing->id)->update([
                'combo_text'  => json_encode($selectedFlat),
                'price'       => $mrpTotal,
                'offer_price' => $finalPrice,
                'images'      => $offerImageFile, // keep image in sync if re-adding
                'updated_at'  => $now,
            ]);
        } else {
            DB::table('carts')->insert([
                'user_id'         => $userId,
                'session_id'      => $sessionId,
                'product_id'      => 0,
                'product_name'    => $offer->offer_name,
                'offer_id'        => $offerId,        // ★ integer, not null
                'combo'           => 'offer',
                'combo_text'      => json_encode($selectedFlat),
                'quantity'        => 1,
                'price'           => $mrpTotal,       // MRP for strikethrough
                'offer_price'     => $finalPrice,     // actual charge
                'images'          => $offerImageFile, // ★ plain "1778739765_offer.png"
                'category_id'     => null,
                'sub_category_id' => null,
                'slug'            => 'bundle-offer-' . $offerId,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        $cartCountQuery = DB::table('carts');
        $userId ? $cartCountQuery->where('user_id', $userId) : $cartCountQuery->where('session_id', $sessionId);
        $cartCount = $cartCountQuery->count();

        return response()->json(['success' => true, 'cart_count' => $cartCount, 'message' => 'Bundle added to cart!']);
    }

    public function storeCheckoutData(Request $request)
    {
        session(['checkout_cart' => $request->cart]);
        return response()->json(['success' => true]);
    }
}