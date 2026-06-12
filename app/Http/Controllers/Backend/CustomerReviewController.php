<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductReview;
use App\Models\ProductsDetails;

class CustomerReviewController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     |  INDEX — list all reviews with product info
     ══════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $query = ProductReview::query()
            ->leftJoin('products_details', 'product_reviews.product_id', '=', 'products_details.id')
            ->select(
                'product_reviews.*',
                'products_details.product_name',
                'products_details.images as product_images',
                'products_details.slug as product_slug'
            );

        /* Optional filters */
        if ($request->filled('status')) {
            $query->where('product_reviews.is_approved', $request->status);
        }
        if ($request->filled('rating')) {
            $query->where('product_reviews.rating', $request->rating);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('product_reviews.reviewer_name', 'like', "%{$s}%")
                  ->orWhere('product_reviews.reviewer_email', 'like', "%{$s}%")
                  ->orWhere('product_reviews.title', 'like', "%{$s}%")
                  ->orWhere('product_reviews.content', 'like', "%{$s}%")
                  ->orWhere('products_details.product_name', 'like', "%{$s}%");
            });
        }

        $reviews = $query->orderBy('product_reviews.created_at', 'desc')->get();

        /* Counts for stat boxes */
        $stats = [
            'total'    => ProductReview::count(),
            'approved' => ProductReview::where('is_approved', 1)->count(),
            'pending'  => ProductReview::where('is_approved', 0)->count(),
            'avg'      => round(ProductReview::where('is_approved', 1)->avg('rating') ?? 0, 1),
        ];

        return view('backend.customer-review', compact('reviews', 'stats'));
    }

    /* ══════════════════════════════════════════════════════════
     |  TOGGLE APPROVAL (AJAX)
     ══════════════════════════════════════════════════════════ */
    public function toggleApproval($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->is_approved = $review->is_approved ? 0 : 1;
        $review->save();

        return response()->json([
            'success'     => true,
            'is_approved' => $review->is_approved,
            'message'     => $review->is_approved ? 'Review approved' : 'Review unapproved',
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     |  VIEW SINGLE REVIEW (AJAX modal)
     ══════════════════════════════════════════════════════════ */
    public function view($id)
    {
        $review = ProductReview::leftJoin('products_details', 'product_reviews.product_id', '=', 'products_details.id')
            ->select(
                'product_reviews.*',
                'products_details.product_name',
                'products_details.images as product_images'
            )
            ->where('product_reviews.id', $id)
            ->firstOrFail();

        $media = [];
        if (!empty($review->media)) {
            $decoded = json_decode($review->media, true);
            if (is_array($decoded)) $media = $decoded;
        }

        /* Product image (first) */
        $productImage = null;
        if (!empty($review->product_images)) {
            $imgs = json_decode($review->product_images, true);
            if (is_array($imgs) && !empty($imgs)) {
                $raw = $imgs[0];
                $productImage = str_starts_with($raw, 'http')
                    ? $raw
                    : asset('signage/home/productimage/' . basename($raw));
            }
        }

        return response()->json([
            'success'      => true,
            'review'       => $review,
            'media'        => $media,
            'productImage' => $productImage,
            'mediaBaseUrl' => asset('signage/home/reviews') . '/',
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     |  DELETE
     ══════════════════════════════════════════════════════════ */
    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);

        /* Delete attached media files (best effort) */
        if (!empty($review->media)) {
            $files = json_decode($review->media, true) ?? [];
            foreach ($files as $file) {
                $path = public_path('signage/home/reviews/' . basename($file));
                if (file_exists($path)) @unlink($path);
            }
        }

        $review->delete();

        return response()->json(['success' => true, 'message' => 'Review deleted']);
    }
}