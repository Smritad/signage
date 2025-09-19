<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\ProductsDetails;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        $userId = auth()->id(); // Optional: handle guest separately
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        if($wishlist) {
            // Remove from wishlist
            $wishlist->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Product removed from wishlist!'
            ]);
        } else {
            // Add to wishlist
            $product = ProductsDetails::findOrFail($productId);
            Wishlist::create([
                'user_id' => $userId,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'slug' => $product->slug,
                'price' => $product->price,
                'offer_price' => $product->offer_price,
                'images' => $product->images,
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Product added to wishlist!'
            ]);
        }
    }
}
