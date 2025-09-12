<?php

namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\ProductsDetails;
use Illuminate\Http\Request;

class ProductsDetailsController extends Controller
{
    public function index()
    {
        
        return view('backend.product-page.products-details.index');
    }

    public function create()
    {
        return view('backend.product-page.products-details.create');
    }

    public function store(Request $request)
    {

        ProductsDetails::create([
            'title'      => $request->title,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('products-details.index')
            ->with('success', 'Perfume note created successfully.');
    }

    public function edit($id)
    {
        $note = ProductsDetails::findOrFail($id);
        return view('backend.product-page.products-details.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $note = ProductsDetails::findOrFail($id);
        $note->update([
            'title'      => $request->title,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('perfume-notes-details.index')
            ->with('success', 'Perfume note updated successfully.');
    }

    public function destroy($id)
    {
       
        $note->update([
            'deleted_by' => auth()->id(),
        ]);
        $note->delete();

        return redirect()->route('perfume-notes-details.index')
            ->with('success', 'Perfume note deleted successfully.');
    }
}
