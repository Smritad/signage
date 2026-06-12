<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\ProductsDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductReviewController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     |  STORE REVIEW
     ══════════════════════════════════════════════════════════ */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products_details,id',
            'rating'         => 'required|integer|min:1|max:5',
            'title'          => 'required|string|max:255',
            'content'        => 'required|string|max:5000',
            'reviewer_name'  => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'media.*'        => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:20480', /* 20 MB */
        ]);

        /* Upload media */
        $mediaFiles = [];
        if ($request->hasFile('media')) {
            $destination = public_path('signage/home/reviews');
            if (!file_exists($destination)) mkdir($destination, 0777, true);

            foreach ($request->file('media') as $file) {
                $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($destination, $filename);
                $mediaFiles[] = $filename;
            }
        }

        $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;

        ProductReview::create([
            'product_id'     => $request->product_id,
            'user_id'        => $userId,
            'rating'         => (int) $request->rating,
            'title'          => $request->title,
            'content'        => $request->content,
            'reviewer_name'  => $request->reviewer_name,
            'reviewer_email' => $request->reviewer_email,
            'media'          => !empty($mediaFiles) ? json_encode($mediaFiles) : null,
            'is_approved'    => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your review has been submitted.',
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     |  LOAD MORE (AJAX pagination)
     ══════════════════════════════════════════════════════════ */
    public function loadMore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products_details,id',
            'page'       => 'nullable|integer|min:1',
            'sort'       => 'nullable|in:recent,highest,lowest',
        ]);

        $perPage = 5;
        $page    = $request->page ?? 1;
        $sort    = $request->sort ?? 'recent';

        $query = ProductReview::where('product_id', $request->product_id)
            ->where('is_approved', 1);

        switch ($sort) {
            case 'highest':  $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc'); break;
            case 'lowest':   $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');  break;
            default:         $query->orderBy('created_at', 'desc'); break;
        }

        $reviews = $query->paginate($perPage, ['*'], 'page', $page);

        $html = '';
        foreach ($reviews as $review) {
            $html .= $this->renderReviewItem($review);
        }

        return response()->json([
            'html'        => $html,
            'has_more'    => $reviews->hasMorePages(),
            'current'     => $reviews->currentPage(),
            'last_page'   => $reviews->lastPage(),
        ]);
    }

    /* Render single review row (used by AJAX + initial load) */
    public static function renderReviewItem($review): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $cls = $i <= $review->rating ? 'icon-star text-star' : 'icon-star text-muted';
            $stars .= '<i class="' . $cls . '"></i>';
        }

        $date = $review->created_at ? $review->created_at->diffForHumans() : '';
        $name = e($review->reviewer_name);
        $initials = strtoupper(mb_substr($review->reviewer_name, 0, 1));

        /* Media thumbnails */
        $mediaHtml = '';
        $media = is_array($review->media) ? $review->media : (json_decode($review->media, true) ?? []);
        if (!empty($media)) {
            $mediaHtml .= '<div class="review-media mt-2">';
            foreach ($media as $m) {
                $ext = strtolower(pathinfo($m, PATHINFO_EXTENSION));
                $url = asset('signage/home/reviews/' . $m);
                if (in_array($ext, ['mp4', 'mov'])) {
                    $mediaHtml .= '<video src="' . $url . '" controls class="review-media-thumb"></video>';
                } else {
                    $mediaHtml .= '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="review-media-thumb"></a>';
                }
            }
            $mediaHtml .= '</div>';
        }

        return '
        <div class="review-item border-bottom py-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="review-stars">' . $stars . '</div>
                <span class="text-muted small">' . $date . '</span>
            </div>
            <div class="d-flex align-items-center mb-2">
                <span class="review-avatar">' . $initials . '</span>
                <strong class="ms-2">' . $name . '</strong>
            </div>
            <h6 class="fw-bold mb-1">' . e($review->title) . '</h6>
            <p class="mb-0 text-muted">' . nl2br(e($review->content)) . '</p>
            ' . $mediaHtml . '
        </div>';
    }
}