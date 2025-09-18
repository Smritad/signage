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
        $products = ProductsDetails::latest()->paginate(10);
        return view('backend.product-page.products-details.index', compact('products'));
    }

    public function create()
    {
        $categories = CategoryDetails::all();
        $subCategories = SabCategoryDetails::all();
        $perfumeNotes = PerfumeNotesDetails::all();
        $fragranceTypes = FragranceTypeDetails::all();
        $perfumeNotesLevel = PerfumeNotesLevelDetails::all();

        return view('backend.product-page.products-details.create', compact(
            'categories', 'subCategories', 'perfumeNotes', 'fragranceTypes', 'perfumeNotesLevel'
        ));
    }


public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:category_details,id',
        'sub_category_id' => 'required|exists:sab_category_details,id',
        'product_name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'product_sku' => 'nullable|string|unique:products_details,product_sku',
        'discount' => 'nullable|numeric',
        'quantity' => 'required|integer',
        'estimate_delivery' => 'nullable|string|max:255',
        'return_policy' => 'nullable|string|max:255',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        // ✅ updated validation
        'perfume_notes_details' => 'nullable|array',
        'perfume_notes_details.*.note_ids' => 'nullable|array',
        'perfume_notes_details.*.note_ids.*' => 'exists:perfume_notes_details,id',
        'perfume_notes_details.*.level_id' => 'nullable|exists:perfume_notes_level_details,id',

        'fragrance_type_id' => 'required|exists:fragrance_type_details,id',
        'measurement_unit' => 'nullable|string|max:255',
        'offer_price' => 'nullable|string|max:255',

        'faqs' => 'nullable|array',
        'faqs.*.question' => 'nullable|string|max:500',
        'faqs.*.answer' => 'nullable|string|max:1000',
        'perfume_details' => 'nullable|array',
        'perfume_details.*.title' => 'nullable|string|max:255',
        'perfume_details.*.icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // Auto-generate SKU if not provided
    $productSku = $request->product_sku ?? strtoupper(Str::random(10));

    // Handle multiple product images
    $images = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/signage/home/productimage'), $filename);
            $images[] = $filename;
        }
    }

    // Handle perfume notes + levels
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

    // Handle perfume details
    $perfumeDetails = [];
    if ($request->has('perfume_details')) {
        foreach ($request->perfume_details as $index => $detail) {
            $iconPath = null;
            if ($request->hasFile("perfume_details.$index.icon")) {
                $file = $request->file("perfume_details.$index.icon");
                $iconName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('/signage/home/productimage'), $iconName);
                $iconPath = $iconName;
            }

            if (!empty($detail['title']) || $iconPath) {
                $perfumeDetails[] = [
                    'title' => $detail['title'] ?? null,
                    'icon'  => $iconPath,
                ];
            }
        }
    }

    // Store product
    ProductsDetails::create([
        'category_id' => $request->category_id,
        'sub_category_id' => $request->sub_category_id,
        'product_name' => $request->product_name,
        'price' => $request->price,
        'product_sku' => $productSku,
        'discount' => $request->discount,
        'quantity' => $request->quantity,
        'estimate_delivery' => $request->estimate_delivery,
        'return_policy' => $request->return_policy,
        'images' => json_encode($images),
        'perfume_notes' => json_encode($perfumeNotesDetails),
        'perfume_details' => json_encode($perfumeDetails),
        'fragrance_type_id' => $request->fragrance_type_id,
        'measurement_unit'  => $request->measurement_unit,
        'offer_price'  => $request->offer_price,

        'description' => $request->description,
        'key_benefits' => $request->key_benefits,
        'how_to_use' => $request->how_to_use,
        'faqs' => json_encode($request->faqs),
        'created_by' => auth()->id(),
    ]);

    return redirect()->route('products-details.index')->with('message', 'Product created successfully.');
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




public function update(Request $request, $id)
{
    $product = ProductsDetails::findOrFail($id);

    // ✅ Validation
    $request->validate([
        'category_id'        => 'required|exists:category_details,id',
        'sub_category_id'    => 'required|exists:sab_category_details,id',
        'product_name'       => 'required|string|max:255',
        'price'              => 'required|numeric',
        'product_sku'        => 'required|string|unique:products_details,product_sku,' . $product->id,
        'discount'           => 'nullable|numeric',
        'quantity'           => 'required|integer',
        'estimate_delivery'  => 'nullable|string|max:255',
        'return_policy'      => 'nullable|string|max:255',
        'images.*'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        // ✅ match store()
        'perfume_notes_details' => 'nullable|array',
        'perfume_notes_details.*.note_ids' => 'nullable|array',
        'perfume_notes_details.*.note_ids.*' => 'exists:perfume_notes_details,id',
        'perfume_notes_details.*.level_id'  => 'nullable|exists:perfume_notes_level_details,id',

        'fragrance_type_id'  => 'required|exists:fragrance_type_details,id',
         'measurement_unit'  => 'nullable|string|max:255',
         'offer_price'  => 'nullable|string|max:255',

        'faqs'               => 'nullable|array',
        'faqs.*.question'    => 'nullable|string|max:500',
        'faqs.*.answer'      => 'nullable|string|max:1000',
        'perfume_details'             => 'nullable|array',
        'perfume_details.*.title'     => 'nullable|string|max:255',
        'perfume_details.*.icon'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

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
        'sub_category_id'    => $request->sub_category_id,
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
        'fragrance_type_id'  => $request->fragrance_type_id,
        'measurement_unit'  => $request->measurement_unit,
        'offer_price'  => $request->offer_price,

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
