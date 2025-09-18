<?php

namespace App\Http\Controllers\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\PerfumeNotesLevelDetails;
use Illuminate\Http\Request;

class PerfumeNotesLevelDetailsController extends Controller
{
    public function index()
    {
        $notes = PerfumeNotesLevelDetails::latest()->get();
        return view('backend.product-page.perfume-notes-level-details.index', compact('notes'));
    }

    public function create()
    {
        return view('backend.product-page.perfume-notes-level-details.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        PerfumeNotesLevelDetails::create([
            'title'      => $request->title,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('perfume-notes-level-details.index')
            ->with('success', 'Perfume note created successfully.');
    }

    public function edit($id)
    {
        $note = PerfumeNotesLevelDetails::findOrFail($id);
        return view('backend.product-page.perfume-notes-level-details.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $note = PerfumeNotesLevelDetails::findOrFail($id);
        $note->update([
            'title'      => $request->title,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('perfume-notes-level-details.index')
            ->with('success', 'Perfume note level updated successfully.');
    }

    public function destroy($id)
    {
        $note = PerfumeNotesLevelDetails::findOrFail($id);
        $note->update([
            'deleted_by' => auth()->id(),
        ]);
        $note->delete();

        return redirect()->route('perfume-notes-level-details.index')
            ->with('success', 'Perfume note level deleted successfully.');
    }
}
