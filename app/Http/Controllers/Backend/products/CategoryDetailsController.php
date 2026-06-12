<?php
namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategoryDetailsController extends Controller
{
    public function index()
{
    $categories = \App\Models\CategoryDetails::latest()->get();
    return view('backend.product-page.category-details.index', compact('categories'));
}



       public function create(Request $request)
    { 
        return view('backend.product-page.category-details.create');
    }



public function store(Request $request)
{
    $request->validate([
        'category_name' => 'required|string|max:255|unique:category_details,category_name',
        'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
    ]);

    $imageName = null;
    if ($request->hasFile('category_image')) {
        $file = $request->file('category_image');
        $imageName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('signage/home/productimage'), $imageName);
    }

    \App\Models\CategoryDetails::create([
        'category_name' => $request->category_name,
        'slug'          => Str::slug($request->category_name),
        'image'         => $imageName,
        'created_by'    => Auth::id(),
        'updated_by'    => Auth::id(),
        'created_at'    => Carbon::now(),
        'updated_at'    => Carbon::now(),
    ]);

    return redirect()->route('category-details.index')->with('message', 'Category added successfully!');
}


public function update(Request $request, $id)
{
    $category = \App\Models\CategoryDetails::findOrFail($id);

    $request->validate([
        'category_name' => 'required|string|max:255|unique:category_details,category_name,' . $id,
        'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
    ]);

    // Handle new image
    $imageName = $category->image;
    if ($request->hasFile('category_image')) {
        // Delete old image
        if ($imageName && file_exists(public_path('signage/home/productimage/'.$imageName))) {
            unlink(public_path('signage/home/productimage/'.$imageName));
        }
        $file = $request->file('category_image');
        $imageName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('signage/home/productimage'), $imageName);
    }

    $category->update([
        'category_name' => $request->category_name,
        'slug'          => Str::slug($request->category_name),
        'image'         => $imageName,
        'updated_by'    => Auth::id(),
        'updated_at'    => Carbon::now(),
    ]);

    return redirect()->route('category-details.index')->with('message', 'Category updated successfully!');
}



public function edit($id)
{
    $category = \App\Models\CategoryDetails::findOrFail($id);
    return view('backend.product-page.category-details.edit', compact('category'));
}


public function destroy($id)
{
    $category = \App\Models\CategoryDetails::findOrFail($id);
    $category->update([
        'deleted_by' => \Illuminate\Support\Facades\Auth::id(),
        'deleted_at' => \Carbon\Carbon::now(),
    ]);
    $category->delete();

    return redirect()->route('category-details.index')->with('message', 'Category deleted successfully!');
}



}