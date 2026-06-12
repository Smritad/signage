<?php

namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\ProductsDetails;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\PerfumeNotesDetails;
use App\Models\PerfumeNotesLevelDetails;
use App\Models\FragranceTypeDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductsDetailsController extends Controller
{
   public function index()
{
    $products = ProductsDetails::whereNull('deleted_at') // exclude soft deleted
                    ->latest()
                    ->get(); // removed paginate

    return view('backend.product-page.products-details.index', compact('products'));
}

   
/*
 * =====================================================================
 * Replace ONLY the create() method in your ProductsDetailsController
 * with the version below. Keep store(), update(), edit(), etc. as-is
 * (store() still needs the cloned_images / cloned_icon handling from
 * the previous code I gave you).
 * =====================================================================
 */

public function create(Request $request)
{
    $categories        = CategoryDetails::all();
    $subCategories     = SabCategoryDetails::all();
    $perfumeNotes      = PerfumeNotesDetails::all();
    $fragranceTypes    = FragranceTypeDetails::all();
    $perfumeNotesLevel = PerfumeNotesLevelDetails::all();

    // All products — used to build the "Copy from existing product" dropdown
    $allProducts = ProductsDetails::orderBy('product_name')
                        ->get(['id', 'product_name', 'product_sku']);

    // If ?clone=ID is present, load that product to pre-fill the form
    $cloneProduct = null;
    if ($request->filled('clone')) {
        $cloneProduct = ProductsDetails::find($request->clone);
    }

    return view('backend.product-page.products-details.create', compact(
        'categories', 'subCategories', 'perfumeNotes',
        'fragranceTypes', 'perfumeNotesLevel',
        'cloneProduct', 'allProducts'
    ));
}




/* ============================================================
 |  REPLACE your entire store() method with THIS.
 |  Fixes:
 |    • fragrance_type_id now saved as JSON (was causing the
 |      "Array to string conversion" crash)
 |    • cloned_images[] now copied to disk and saved
 |    • cloned_icon (perfume_details) now copied and saved
 |    • SKU auto-suffixed if duplicate (needed for clone)
 ============================================================ */

public function store(Request $request)
{
    // ─── Validation ────────────────────────────────────────────────────
    $request->validate([
        'category_id'                          => 'required|exists:category_details,id',
        'sub_category_id'                      => 'required|array|min:1',
        'sub_category_id.*'                    => 'exists:sab_category_details,id',

        'product_name'                         => 'required|string|max:255',
        'price'                                => 'required|numeric',
        'offer_price'                          => 'nullable|numeric',
        'product_sku'                          => 'nullable|string',
        'discount'                             => 'nullable|numeric',
        'quantity'                             => 'required|integer',
        'estimate_delivery'                    => 'nullable|string|max:255',
        'return_policy'                        => 'nullable|string|max:255',

        'images'                               => 'nullable|array',
        'images.*'                             => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        'cloned_images'                        => 'nullable|array',
        'cloned_images.*'                      => 'nullable|string',

        'perfume_notes_details'                => 'nullable|array',
        'perfume_notes_details.*.note_ids'     => 'nullable|array',
        'perfume_notes_details.*.note_ids.*'   => 'exists:perfume_notes_details,id',
        'perfume_notes_details.*.level_id'     => 'nullable|exists:perfume_notes_level_details,id',

        // FIX: fragrance_type_id is an ARRAY (multi-select), matches update()
        'fragrance_type_id'                    => 'required|array|min:1',
        'fragrance_type_id.*'                  => 'exists:fragrance_type_details,id',

        'measurement_unit'                     => 'nullable|string|max:255',

        'description'                          => 'nullable|string',
        'key_benefits'                         => 'nullable|string',
        'how_to_use'                           => 'nullable|string',

        'faqs'                                 => 'nullable|array',
        'faqs.*.question'                      => 'nullable|string|max:500',
        'faqs.*.answer'                        => 'nullable|string|max:1000',

        'perfume_details'                      => 'nullable|array',
        'perfume_details.*.title'              => 'nullable|string|max:255',
        'perfume_details.*.icon'               => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        'perfume_details.*.cloned_icon'        => 'nullable|string',
    ]);

    // ─── SKU (auto-generate or suffix if duplicated) ─────────────────
    $productSku = $request->filled('product_sku')
        ? $request->product_sku
        : strtoupper(Str::random(10));

    if (ProductsDetails::where('product_sku', $productSku)->exists()) {
        $productSku = $productSku . '-' . strtoupper(Str::random(4));
    }

    // ─── Slug ────────────────────────────────────────────────────────
    $slug = Str::slug($request->product_name, '-');

    // ─── Product images (CLONED + NEW UPLOADS) ───────────────────────
    $images = [];

    // (a) Cloned images — physically copy each file
    if ($request->has('cloned_images')) {
        foreach ($request->cloned_images as $index => $cloned) {
            // Skip if user uploaded a replacement in this row
            if ($request->hasFile("images.$index")) {
                continue;
            }
            if (!empty($cloned)) {
                $source = public_path('signage/home/productimage/' . $cloned);
                if (file_exists($source)) {
                    $ext     = pathinfo($cloned, PATHINFO_EXTENSION);
                    $newName = Str::random(40) . '.' . $ext;
                    copy($source, public_path('signage/home/productimage/' . $newName));
                    $images[] = $newName;
                }
            }
        }
    }

    // (b) Freshly uploaded images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            if ($file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('signage/home/productimage'), $filename);
                $images[] = $filename;
            }
        }
    }

    // ─── Perfume notes + levels ──────────────────────────────────────
    $perfumeNotesDetails = [];
    if ($request->has('perfume_notes_details')) {
        foreach ($request->perfume_notes_details as $detail) {
            if (!empty($detail['note_ids']) || !empty($detail['level_id'])) {
                $perfumeNotesDetails[] = [
                    'note_ids' => $detail['note_ids'] ?? [],
                    'level_id' => $detail['level_id'] ?? null,
                ];
            }
        }
    }

    // ─── Perfume details (icon + title) — WITH CLONE SUPPORT ────────
    $perfumeDetails = [];
    if ($request->has('perfume_details')) {
        foreach ($request->perfume_details as $index => $detail) {
            $iconPath = null;

            // (a) Newly uploaded icon wins
            if ($request->hasFile("perfume_details.$index.icon")) {
                $file     = $request->file("perfume_details.$index.icon");
                $iconName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('signage/home/productimage'), $iconName);
                $iconPath = $iconName;
            }
            // (b) Otherwise, copy the cloned icon
            elseif (!empty($detail['cloned_icon'])) {
                $source = public_path('signage/home/productimage/' . $detail['cloned_icon']);
                if (file_exists($source)) {
                    $ext     = pathinfo($detail['cloned_icon'], PATHINFO_EXTENSION);
                    $newName = Str::random(40) . '.' . $ext;
                    copy($source, public_path('signage/home/productimage/' . $newName));
                    $iconPath = $newName;
                }
            }

            if (!empty($detail['title']) || $iconPath) {
                $perfumeDetails[] = [
                    'title' => $detail['title'] ?? null,
                    'icon'  => $iconPath,
                ];
            }
        }
    }

    // ─── Create product ──────────────────────────────────────────────
    ProductsDetails::create([
        'category_id'       => $request->category_id,
        'sub_category_id'   => json_encode($request->sub_category_id),

        'product_name'      => $request->product_name,
        'slug'              => $slug,
        'price'             => $request->price,
        'offer_price'       => $request->offer_price,
        'product_sku'       => $productSku,
        'discount'          => $request->discount,
        'quantity'          => $request->quantity,
        'estimate_delivery' => $request->estimate_delivery,
        'return_policy'     => $request->return_policy,

        'images'            => json_encode($images),
        'perfume_notes'     => json_encode($perfumeNotesDetails),
        'perfume_details'   => json_encode($perfumeDetails),

        // FIX: fragrance_type_id must be JSON-encoded (it's an array now)
        'fragrance_type_id' => json_encode($request->fragrance_type_id),
        'measurement_unit'  => $request->measurement_unit,

        'description'       => $request->description,
        'key_benefits'      => $request->key_benefits,
        'how_to_use'        => $request->how_to_use,
        'faqs'              => json_encode($request->faqs),

        'created_by'        => auth()->id(),
    ]);

    return redirect()
        ->route('products-details.index')
        ->with('message', 'Product created successfully.');
}


public function toggleBestseller($id)
{
    $product = ProductsDetails::findOrFail($id);
    $product->is_bestseller = !$product->is_bestseller;
    $product->save();

    return redirect()->back()->with('success', 'Bestseller status updated.');
}

public function toggleNewArrival($id)
{
    $product = ProductsDetails::findOrFail($id);
    $product->is_new_arrival = !$product->is_new_arrival;
    $product->save();

    return redirect()->back()->with('success', 'New Arrival status updated.');
}

    public function edit($id)
{
    $product = ProductsDetails::findOrFail($id);

    $categories       = CategoryDetails::all();
    $subCategories    = SabCategoryDetails::all();
    $perfumeNotes     = PerfumeNotesDetails::all();
    $perfumeNotesLevel = PerfumeNotesLevelDetails::all(); // ✅ added
    $fragranceTypes   = FragranceTypeDetails::all();

    return view('backend.product-page.products-details.edit', compact(
        'product',
        'categories',
        'subCategories',
        'perfumeNotes',
        'perfumeNotesLevel', // ✅ pass to view
        'fragranceTypes'
    ));
}


public function updatePriority(Request $request, $id)
{
    $request->validate([
        'priority' => 'required|integer|min:0'
    ]);

    $product = ProductsDetails::findOrFail($id);
    $product->priority = $request->priority;
    $product->save();

    return redirect()->back()->with('message', 'Priority updated successfully.');
}


public function update(Request $request, $id)
{
    $product = ProductsDetails::findOrFail($id);

    // ✅ Validation
    $request->validate([
        'category_id'        => 'required|exists:category_details,id',
       'sub_category_id'   => 'required|array',
'sub_category_id.*' => 'exists:sab_category_details,id',
        'product_name'       => 'required|string|max:255',
        'price'              => 'required|numeric',
        'product_sku'        => 'required|string|unique:products_details,product_sku,' . $product->id,
        'discount'           => 'nullable|numeric',
        'quantity'           => 'required|integer',
        'estimate_delivery'  => 'nullable|string|max:255',
        'return_policy'      => 'nullable|string|max:255',
'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',

        // ✅ match store()
        'perfume_notes_details' => 'nullable|array',
        'perfume_notes_details.*.note_ids' => 'nullable|array',
        'perfume_notes_details.*.note_ids.*' => 'exists:perfume_notes_details,id',
        'perfume_notes_details.*.level_id'  => 'nullable|exists:perfume_notes_level_details,id',

'fragrance_type_id'  => 'required|array',
'fragrance_type_id.*' => 'exists:fragrance_type_details,id',
         'measurement_unit'  => 'nullable|string|max:255',
         'offer_price'  => 'nullable|string|max:255',

        'faqs'               => 'nullable|array',
        'faqs.*.question'    => 'nullable|string|max:500',
        'faqs.*.answer'      => 'nullable|string|max:1000',
        'perfume_details'             => 'nullable|array',
        'perfume_details.*.title'     => 'nullable|string|max:255',
'perfume_details.*.icon' => 'nullable|mimetypes:image/jpeg,image/png,image/webp,image/svg+xml|max:20480',
    ]);


    $slug = Str::slug($request->product_name, '-');
    /* -----------------------------------------------------------------
     |  Images Handling
     -----------------------------------------------------------------*/
    $images = $request->old_images ?? [];

    if ($request->has('deleted_images')) {
        foreach ($request->deleted_images as $del) {
            $path = public_path('signage/home/productimage/' . $del);
            if (file_exists($path)) {
                unlink($path);
            }
            $images = array_diff($images, [$del]);
        }
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            if ($file) {
                if (isset($images[$index]) && !empty($images[$index])) {
                    $oldPath = public_path('signage/home/productimage/' . $images[$index]);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('signage/home/productimage'), $filename);
                $images[$index] = $filename;
            }
        }
    }
    $images = array_values($images);

    /* -----------------------------------------------------------------
     |  Perfume Notes Handling (✅ same as store)
     -----------------------------------------------------------------*/
    $perfumeNotesDetails = [];
    if ($request->has('perfume_notes_details')) {
        foreach ($request->perfume_notes_details as $detail) {
            if (!empty($detail['note_ids']) || !empty($detail['level_id'])) {
                $perfumeNotesDetails[] = [
                    'note_ids' => $detail['note_ids'] ?? [],
                    'level_id' => $detail['level_id'] ?? null,
                ];
            }
        }
    }

    /* -----------------------------------------------------------------
     |  Perfume Details Handling
     -----------------------------------------------------------------*/
    $perfumeDetails = [];
    if ($request->has('perfume_details')) {
        foreach ($request->perfume_details as $index => $detail) {
            $iconPath = $detail['old_icon'] ?? null;

            if ($request->hasFile("perfume_details.$index.icon")) {
                $file = $request->file("perfume_details.$index.icon");

                if ($iconPath) {
                    $oldIcon = public_path('signage/home/productimage/' . $iconPath);
                    if (file_exists($oldIcon)) {
                        unlink($oldIcon);
                    }
                }

                $iconName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('signage/home/productimage'), $iconName);
                $iconPath = $iconName;
            }

            $perfumeDetails[] = [
                'title' => $detail['title'] ?? null,
                'icon'  => $iconPath,
            ];
        }
    }

    /* -----------------------------------------------------------------
     |  Update Product
     -----------------------------------------------------------------*/
    $product->update([
        'category_id'        => $request->category_id,
        'sub_category_id' => json_encode($request->sub_category_id),
        'product_name'       => $request->product_name,
        'price'              => $request->price,
        'product_sku'        => $request->product_sku,
        'discount'           => $request->discount,
        'quantity'           => $request->quantity,
        'estimate_delivery'  => $request->estimate_delivery,
        'return_policy'      => $request->return_policy,
        'images'             => json_encode($images),
        'perfume_notes'      => json_encode($perfumeNotesDetails), // ✅ fixed
        'perfume_details'    => json_encode($perfumeDetails),
'fragrance_type_id' => json_encode($request->fragrance_type_id),
        'measurement_unit'  => $request->measurement_unit,
        'offer_price'  => $request->offer_price,
        'slug'  => $slug,

        'description'        => $request->description,
        'key_benefits'       => $request->key_benefits,
        'how_to_use'         => $request->how_to_use,
        'faqs'               => json_encode($request->faqs),
        'updated_by'         => auth()->id(),
    ]);

    return redirect()->route('products-details.index')
        ->with('message', 'Product updated successfully.');
}



    public function destroy($id)
{
    $product = ProductsDetails::findOrFail($id);

    $product->update(['deleted_by' => auth()->id()]);
    $product->delete(); // if SoftDeletes is enabled → soft delete

    return redirect()->route('products-details.index')
                     ->with('message', 'Product deleted successfully.');
}

}
