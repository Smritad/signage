<?php
namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\SabCategoryDetails;
use App\Models\CategoryDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class SabCategoryDetailsController extends Controller
{
    public function index()
    {
        $subcategories = SabCategoryDetails::with('category')->latest()->get();
        return view('backend.product-page.sab-category-details.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = CategoryDetails::all();
        return view('backend.product-page.sab-category-details.create', compact('categories'));
    }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:category_details,id',
        'sab_category_name' => 'required|string|max:255|unique:sab_category_details,sab_category_name',
    ]);

    SabCategoryDetails::create([
        'category_id' => $request->category_id,
        'sab_category_name' => $request->sab_category_name, // FIXED
        'slug' => Str::slug($request->sab_category_name),  // FIXED
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('sab-category-details.index')->with('success', 'Sab Category created successfully.');
}


    public function edit($id)
    {
        $subcategory = SabCategoryDetails::findOrFail($id);
        $categories = CategoryDetails::all();
        return view('backend.product-page.sab-category-details.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $subcategory = SabCategoryDetails::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:category_details,id',
            'sab_category_name' => 'required|string|max:255|unique:sab_category_details,sab_category_name,' . $subcategory->id,
        ]);

        $subcategory->update([
            'category_id' => $request->category_id,
            'sab_category_name' => $request->sab_category_name,
            'slug' => Str::slug($request->sab_category_name),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('sab-category-details.index')->with('success', 'Sub Category updated successfully.');
    }

    public function destroy($id)
    {
        $subcategory = SabCategoryDetails::findOrFail($id);
        $subcategory->delete();

        return redirect()->route('sab-category-details.index')->with('success', 'Sub Category deleted successfully.');
    }
}
