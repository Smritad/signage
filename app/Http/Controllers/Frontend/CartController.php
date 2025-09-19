<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductsDetails;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $product = ProductsDetails::findOrFail($request->product_id);
        $images = json_decode($product->images, true);

        $cartItem = Cart::create([
            'user_id' => auth()->id(), // optional if logged in
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'slug' => $product->slug,
            'price' => $product->price,
            'offer_price' => $product->offer_price ?? null,
            'quantity' => $request->quantity ?? 1,
            'images' => json_encode($images),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cartItem' => $cartItem
        ]);
    }
}
