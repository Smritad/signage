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
        'category_name' => 'required|string|max:255|unique:category_details,category_name'
    ]);

    \App\Models\CategoryDetails::create([
        'category_name' => $request->category_name,
        'slug'          => \Illuminate\Support\Str::slug($request->category_name),
        'created_by'    => \Illuminate\Support\Facades\Auth::id(),
        'updated_by'    => \Illuminate\Support\Facades\Auth::id(),
        'created_at'    => \Carbon\Carbon::now(),
        'updated_at'    => \Carbon\Carbon::now(),
    ]);

    return redirect()->route('category-details.index')->with('message', 'Category added successfully!');
}


public function edit($id)
{
    $category = \App\Models\CategoryDetails::findOrFail($id);
    return view('backend.product-page.category-details.edit', compact('category'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'category_name' => 'required|string|max:255|unique:category_details,category_name,' . $id
    ]);

    $category = \App\Models\CategoryDetails::findOrFail($id);
    $category->update([
        'category_name' => $request->category_name,
        'slug'          => \Illuminate\Support\Str::slug($request->category_name),
        'updated_by'    => \Illuminate\Support\Facades\Auth::id(),
        'updated_at'    => \Carbon\Carbon::now(),
    ]);

    return redirect()->route('category-details.index')->with('message', 'Category updated successfully!');
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