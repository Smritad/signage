<?php

namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\FragranceTypeDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class FragranceTypeDetailsController extends Controller
{
    public function index()
    {
        $notes = FragranceTypeDetails::latest()->get();
        return view('backend.product-page.fragrance-type-details.index', compact('notes'));
    }

    public function create()
    {
        return view('backend.product-page.fragrance-type-details.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
    ]);

    $imageName = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $imageName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('signage/home/productimage'), $imageName);
    }

    FragranceTypeDetails::create([
        'title'      => $request->title,
        'slug'       => Str::slug($request->title),
        'image'      => $imageName,
        'created_by' => auth()->id(),
        'updated_by' => auth()->id(),
    ]);

    return redirect()->route('fragrance-type-details.index')
        ->with('success', 'Fragrance Type created successfully.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
    ]);

    $note = FragranceTypeDetails::findOrFail($id);

    $imageName = $note->image;
    if ($request->hasFile('image')) {
        // Delete old image
        if ($imageName && file_exists(public_path('signage/home/productimage/'.$imageName))) {
            unlink(public_path('signage/home/productimage/'.$imageName));
        }
        $file = $request->file('image');
        $imageName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('signage/home/productimage'), $imageName);
    }

    $note->update([
        'title'      => $request->title,
        'slug'       => Str::slug($request->title),
        'image'      => $imageName,
        'updated_by' => auth()->id(),
    ]);

    return redirect()->route('fragrance-type-details.index')
        ->with('success', 'Fragrance Type updated successfully.');
}


    public function edit($id)
    {
        $note = FragranceTypeDetails::findOrFail($id);
        return view('backend.product-page.fragrance-type-details.edit', compact('note'));
    }

   

    public function destroy($id)
    {
        $note = FragranceTypeDetails::findOrFail($id);
        $note->update([
            'deleted_by' => auth()->id(),
        ]);
        $note->delete();

        return redirect()->route('fragrance-type-details.index')
            ->with('success', 'Fragrance Type deleted successfully.');
    }
}
