<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CarzyDealController extends Controller
{
    /* ─────────────────────────────────────────────
     | List All Active Offers
     ───────────────────────────────────────────── */
    public function index()
{
    $today = now()->toDateString();

    $offers = DB::table('offers')
        ->where('is_active', 1)
        ->whereNull('deleted_at')
        // Validity window: hide before start_date and after end_date.
        ->where(function ($q) use ($today) {
            $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
        })
        ->where(function ($q) use ($today) {
            $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
        })
        ->orderByRaw('priority = 0')   // admin-set priority first, 0 = last
        ->orderBy('priority', 'asc')   // 1, 2, 3 ...
        ->orderByDesc('id')            // tie-breaker (newest first)
        ->paginate(8);

    return view('frontend.crazydeal', compact('offers'));
}


    /* ─────────────────────────────────────────────
     | Show Single Offer By Slug
     ───────────────────────────────────────────── */
    public function show($slug)
    {
        $today = now()->toDateString();

        $offer = DB::table('offers')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
            ->first();

        if (!$offer) {
            abort(404);
        }

        $slots        = json_decode($offer->products, true) ?? [];
        $stepProducts = [];

        foreach ($slots as $index => $slot) {

            $slotType           = $slot['slot_type'] ?? 'specific';
            $qty                = (int) ($slot['qty'] ?? 1);
            $categoryId         = $slot['category_id'] ?? null;
            $specificProductIds = $slot['specific_product_ids'] ?? [];
            $pinnedProductIds   = $slot['pinned_product_ids'] ?? [];
            $units              = $slot['units'] ?? [];
            $label              = $slot['slot_label'] ?? 'Select Product';

            $products = collect();

            switch ($slotType) {

                /* ─────────────────────────────
                 | Specific Products
                 ───────────────────────────── */
                case 'specific':

                    if (!empty($specificProductIds)) {

                        $products = DB::table('products_details')
                            ->whereIn('id', $specificProductIds)
                            ->where('is_active', 1)
                            ->whereNull('deleted_at')
                            ->get();
                    }

                    break;

                /* ─────────────────────────────
                 | Full Category Products
                 ───────────────────────────── */
                case 'category':

                    if ($categoryId) {

                        $query = DB::table('products_details')
                            ->where('category_id', $categoryId)
                            ->where('is_active', 1)
                            ->whereNull('deleted_at');

                        if (!empty($units)) {
                            $query->whereIn('measurement_unit', $units);
                        }

                        $products = $query->get();
                    }

                    break;

                /* ─────────────────────────────
                 | Category Pinned Products
                 ───────────────────────────── */
                case 'category_pinned':

                    $filterIds = !empty($pinnedProductIds)
                        ? $pinnedProductIds
                        : $specificProductIds;

                    if (!empty($filterIds)) {

                        $query = DB::table('products_details')
                            ->whereIn('id', $filterIds)
                            ->where('is_active', 1)
                            ->whereNull('deleted_at');

                        if (!empty($units)) {
                            $query->whereIn('measurement_unit', $units);
                        }

                        $products = $query->get();

                    } elseif ($categoryId) {

                        $query = DB::table('products_details')
                            ->where('category_id', $categoryId)
                            ->where('is_active', 1)
                            ->whereNull('deleted_at');

                        if (!empty($units)) {
                            $query->whereIn('measurement_unit', $units);
                        }

                        $products = $query->get();
                    }

                    break;

                /* ─────────────────────────────
                 | Sub Category Products
                 ───────────────────────────── */
                case 'sub_category':

                    $subCategoryId = $slot['sub_category_id'] ?? null;

                    if ($subCategoryId) {

                        $products = DB::table('products_details')
                            ->where('is_active', 1)
                            ->whereNull('deleted_at')
                            ->whereRaw(
                                "JSON_CONTAINS(sub_category_id, ?)",
                                [json_encode((string) $subCategoryId)]
                            )
                            ->get();
                    }

                    break;

                default:
                    break;
            }

            $stepProducts[] = [
                'step_no'  => $index + 1,
                'title'    => $label,
                'qty'      => $qty,
                'products' => $products,
            ];
        }

        return view('frontend.show', compact('offer', 'stepProducts'));
    }
}