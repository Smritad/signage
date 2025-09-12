<?php

namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\PerfumeNotesDetails;
use Illuminate\Http\Request;

class PerfumeNotesDetailsController extends Controller
{
    public function index()
    {
        $notes = PerfumeNotesDetails::latest()->get();
        return view('backend.product-page.perfume-notes-details.index', compact('notes'));
    }

    public function create()
    {
        return view('backend.product-page.perfume-notes-details.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        PerfumeNotesDetails::create([
            'title'      => $request->title,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('perfume-notes-details.index')
            ->with('success', 'Perfume note created successfully.');
    }

    public function edit($id)
    {
        $note = PerfumeNotesDetails::findOrFail($id);
        return view('backend.product-page.perfume-notes-details.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $note = PerfumeNotesDetails::findOrFail($id);
        $note->update([
            'title'      => $request->title,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('perfume-notes-details.index')
            ->with('success', 'Perfume note updated successfully.');
    }

    public function destroy($id)
    {
        $note = PerfumeNotesDetails::findOrFail($id);
        $note->update([
            'deleted_by' => auth()->id(),
        ]);
        $note->delete();

        return redirect()->route('perfume-notes-details.index')
            ->with('success', 'Perfume note deleted successfully.');
    }
}
