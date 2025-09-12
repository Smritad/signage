<?php

namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\FragranceTypeDetails;
use Illuminate\Http\Request;

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
        $request->validate(['title' => 'required|string|max:255']);

        FragranceTypeDetails::create([
            'title'      => $request->title,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('fragrance-type-details.index')
            ->with('success', 'Fragrance Type created successfully.');
    }

    public function edit($id)
    {
        $note = FragranceTypeDetails::findOrFail($id);
        return view('backend.product-page.fragrance-type-details.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $note = FragranceTypeDetails::findOrFail($id);
        $note->update([
            'title'      => $request->title,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('fragrance-type-details.index')
            ->with('success', 'Fragrance Type updated successfully.');
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
