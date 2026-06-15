<?php

namespace App\Http\Controllers\Backend\offer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    /* ─────────────────────────────────────────────────────────────
       LIST
    ───────────────────────────────────────────────────────────── */
    public function index()
    {
        $offers = DB::table('offers')
            ->whereNull('deleted_by')
            ->latest()
            ->get()
            ->map(function ($offer) {
                $offer->products_decoded = json_decode($offer->products, true) ?? [];
                return $offer;
            });

        return view('backend.offer-page.offers-details.index', compact('offers'));
    }

    /* ─────────────────────────────────────────────────────────────
       CREATE PAGE
    ───────────────────────────────────────────────────────────── */
    public function create()
    {
        $products = DB::table('products_details')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->select('id', 'product_name', 'category_id', 'price_variants')
            ->orderBy('product_name')
            ->get();

        $categories = DB::table('category_details')
            ->whereNull('deleted_at')
            ->orderBy('category_name')
            ->get();

        return view('backend.offer-page.offers-details.create', compact('products', 'categories'));
    }

    /* ─────────────────────────────────────────────────────────────
       STORE
    ───────────────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $rules = [
            'offer_name'       => 'required|string|max:255',
            'offer_price_type' => 'required|in:fixed,percent',
            'products'         => 'required|string',
            'banner_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'offer_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($request->offer_price_type === 'percent') {
            $rules['offer_discount_percent'] = 'required|numeric|min:0.01|max:100';
        } else {
            $rules['offer_price'] = 'required|numeric|min:0';
        }

        $request->validate($rules, [
            'banner_image.max' => 'The banner image must not be larger than 2 MB.',
            'offer_image.max'  => 'The offer image must not be larger than 2 MB.',
        ]);

        $productsArr = json_decode($request->products, true);

        if (!is_array($productsArr) || empty($productsArr)) {
            return back()->withErrors(['products' => 'Please add at least one slot.'])->withInput();
        }

        $result = $this->validateAndNormaliseSlots($productsArr);
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }

        [$bannerImage, $offerImage] = $this->handleImageUploads($request, null, null);

        $offerPrice = $request->offer_price_type === 'percent'
            ? $request->offer_discount_percent
            : $request->offer_price;

        $slug = $this->generateUniqueSlug($request->offer_name);

        DB::table('offers')->insert([
            'offer_name'       => $request->offer_name,
            'slug'             => $slug,
            'offer_price_type' => $request->offer_price_type,
            'offer_price'      => $offerPrice,
            'products'         => json_encode($result),
            'banner_image'     => $bannerImage,
            'offer_image'      => $offerImage,
            'is_active'        => $request->is_active ?? 1,
            'created_by'       => Auth::id(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->route('offer-details.index')->with('success', 'Offer created successfully.');
    }

    /* ─────────────────────────────────────────────────────────────
       EDIT PAGE
    ───────────────────────────────────────────────────────────── */
    public function edit($id)
    {
        $offer = DB::table('offers')->where('id', $id)->whereNull('deleted_by')->first();

        if (!$offer) abort(404);

        $offer->products_decoded = json_decode($offer->products, true) ?? [];

        $products = DB::table('products_details')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->select('id', 'product_name', 'category_id', 'price_variants')
            ->orderBy('product_name')
            ->get();

        $categories = DB::table('category_details')
            ->whereNull('deleted_at')
            ->orderBy('category_name')
            ->get();

        return view('backend.offer-page.offers-details.edit', compact('offer', 'products', 'categories'));
    }

    /* ─────────────────────────────────────────────────────────────
       UPDATE
    ───────────────────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $offer = DB::table('offers')->where('id', $id)->whereNull('deleted_by')->first();
        if (!$offer) abort(404);

        $rules = [
            'offer_name'       => 'required|string|max:255',
            'offer_price_type' => 'required|in:fixed,percent',
            'products'         => 'required|string',
            'banner_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'offer_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($request->offer_price_type === 'percent') {
            $rules['offer_discount_percent'] = 'required|numeric|min:0.01|max:100';
        } else {
            $rules['offer_price'] = 'required|numeric|min:0';
        }

        $request->validate($rules, [
            'banner_image.max' => 'The banner image must not be larger than 2 MB.',
            'offer_image.max'  => 'The offer image must not be larger than 2 MB.',
        ]);

        $productsArr = json_decode($request->products, true);

        if (!is_array($productsArr) || empty($productsArr)) {
            return back()->withErrors(['products' => 'Please add at least one slot.'])->withInput();
        }

        $result = $this->validateAndNormaliseSlots($productsArr);
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }

        [$bannerImage, $offerImage] = $this->handleImageUploads(
            $request,
            $offer->banner_image,
            $offer->offer_image
        );

        $offerPrice = $request->offer_price_type === 'percent'
            ? $request->offer_discount_percent
            : $request->offer_price;

        // Regenerate slug only when the name has changed
        $slug = ($offer->offer_name !== $request->offer_name)
            ? $this->generateUniqueSlug($request->offer_name, $id)
            : $offer->slug;

        DB::table('offers')->where('id', $id)->update([
            'offer_name'       => $request->offer_name,
            'slug'             => $slug,
            'offer_price_type' => $request->offer_price_type,
            'offer_price'      => $offerPrice,
            'products'         => json_encode($result),
            'banner_image'     => $bannerImage,
            'offer_image'      => $offerImage,
            'is_active'        => $request->is_active ?? 1,
            'updated_by'       => Auth::id(),
            'updated_at'       => now(),
        ]);

        return redirect()->route('offer-details.index')->with('success', 'Offer updated successfully.');
    }

    /* ─────────────────────────────────────────────────────────────
       DELETE
    ───────────────────────────────────────────────────────────── */
    public function destroy($id)
    {
        DB::table('offers')->where('id', $id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => now(),
        ]);

        return redirect()->route('offer-details.index')->with('success', 'Offer deleted successfully.');
    }

    /* ─────────────────────────────────────────────────────────────
       TOGGLE STATUS
    ───────────────────────────────────────────────────────────── */
    public function toggleStatus($id)
    {
        $offer = DB::table('offers')->where('id', $id)->first();

        if (!$offer) {
            return response()->json(['success' => false]);
        }

        DB::table('offers')->where('id', $id)->update([
            'is_active'  => $offer->is_active ? 0 : 1,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'is_active' => !$offer->is_active]);
    }

    /* ═══════════════════════════════════════════════════════════════
       PRIVATE HELPERS
    ═══════════════════════════════════════════════════════════════ */

    /**
     * Generate a unique slug from the offer name.
     * If "summer-sale" exists, returns "summer-sale-1", then "summer-sale-2", etc.
     * Excludes the current record ($exceptId) so an update to the same name keeps its slug.
     */
    private function generateUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base  = Str::slug($name);
        $slug  = $base;
        $count = 1;

        while (
            DB::table('offers')
                ->where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->whereNull('deleted_by')
                ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Validate and normalise product slots.
     */
    private function validateAndNormaliseSlots(array $slots)
    {
        foreach ($slots as $index => &$slot) {

            if (empty($slot['slot_type'])) {
                return back()
                    ->withErrors(['products' => 'Invalid slot type at row ' . ($index + 1)])
                    ->withInput();
            }

            switch ($slot['slot_type']) {

                case 'category':
                    if (empty($slot['category_id'])) {
                        return back()
                            ->withErrors(['products' => 'Please select a category at row ' . ($index + 1)])
                            ->withInput();
                    }
                    $slot['units']                = $slot['units'] ?? [];
                    $slot['specific_product_ids'] = [];
                    $slot['pinned_product_ids']   = [];
                    break;

                case 'specific':
                    if (empty($slot['specific_product_ids']) || !is_array($slot['specific_product_ids'])) {
                        return back()
                            ->withErrors(['products' => 'Please select at least one product at row ' . ($index + 1)])
                            ->withInput();
                    }
                    $slot['category_id']          = null;
                    $slot['units']                = [];
                    $slot['pinned_product_ids']   = [];
                    break;

                case 'category_pinned':
                    if (empty($slot['category_id'])) {
                        return back()
                            ->withErrors(['products' => 'Please select a category at row ' . ($index + 1)])
                            ->withInput();
                    }
                    if (empty($slot['pinned_product_ids']) || !is_array($slot['pinned_product_ids'])) {
                        return back()
                            ->withErrors(['products' => 'Please pin at least one product at row ' . ($index + 1)])
                            ->withInput();
                    }
                    $slot['units']                = $slot['units'] ?? [];
                    $slot['specific_product_ids'] = [];
                    break;

                default:
                    return back()
                        ->withErrors(['products' => 'Unknown slot type at row ' . ($index + 1)])
                        ->withInput();
            }

            $slot['qty']        = !empty($slot['qty']) ? (int) $slot['qty'] : 1;
            $slot['slot_label'] = $slot['slot_label'] ?? '';
        }
        unset($slot);

        return $slots;
    }

    /**
     * Handle banner + offer image uploads/replacements.
     */
    private function handleImageUploads(Request $request, ?string $existingBanner, ?string $existingOffer): array
    {
        $uploadPath = public_path('offerimage');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $bannerImage = $existingBanner;

        if ($request->hasFile('banner_image') && $request->file('banner_image')->isValid()) {
            if ($bannerImage && File::exists($uploadPath . '/' . $bannerImage)) {
                File::delete($uploadPath . '/' . $bannerImage);
            }
            $file        = $request->file('banner_image');
            $bannerImage = time() . '_banner.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $bannerImage);
        }

        $offerImage = $existingOffer;

        if ($request->hasFile('offer_image') && $request->file('offer_image')->isValid()) {
            if ($offerImage && File::exists($uploadPath . '/' . $offerImage)) {
                File::delete($uploadPath . '/' . $offerImage);
            }
            $file       = $request->file('offer_image');
            $offerImage = time() . '_offer.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $offerImage);
        }

        return [$bannerImage, $offerImage];
    }
}
