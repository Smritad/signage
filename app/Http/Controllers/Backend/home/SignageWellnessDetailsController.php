<?php

namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\SignageWellnessDetails;

class SignageWellnessDetailsController extends Controller
{
    public function index()
    {
        $records = SignageWellnessDetails::whereNull('deleted_at')->orderByDesc('id')->get();
        return view('backend.home-page.signage-wellness-details.index', compact('records'));
    }

    public function create()
    {
        return view('backend.home-page.signage-wellness-details.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required|string',
            'items.*.title' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $items = [];

        foreach ($request->items as $index => $item) {
            $path = null;
            if ($request->hasFile("items.$index.image")) {
                $file = $request->file("items.$index.image");
                $filename = time() . "_$index." . $file->getClientOriginalExtension();
                $path = $file->storeAs('signage_images', $filename, 'public');
            }

            $items[] = [
                'title' => $item['title'],
                'description' => $item['description'],
                'image' => $path,
            ];
        }

        SignageWellnessDetails::create([
            'heading' => $request->heading,
            'items' => json_encode($items),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('signage-wellness-details.index')->with('success', 'Added successfully.');
    }

    public function edit($id)
    {
        $record = SignageWellnessDetails::findOrFail($id);
        return view('backend.home-page.signage-wellness-details.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'heading' => 'required|string',
            'items.*.title' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $record = SignageWellnessDetails::findOrFail($id);
        $existingItems = json_decode($record->items, true) ?? [];
        $items = [];

        foreach ($request->items as $index => $item) {
            $path = $existingItems[$index]['image'] ?? null;

            if ($request->hasFile("items.$index.image")) {
                $file = $request->file("items.$index.image");
                $filename = time() . "_$index." . $file->getClientOriginalExtension();
                $path = $file->storeAs('signage_images', $filename, 'public');
            }

            $items[] = [
                'title' => $item['title'],
                'description' => $item['description'],
                'image' => $path,
            ];
        }

        $record->update([
            'heading' => $request->heading,
            'items' => json_encode($items),
            'modified_by' => Auth::id(),
            'modified_at' => Carbon::now(),
        ]);

        return redirect()->route('signage-wellness-details.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        $record = SignageWellnessDetails::findOrFail($id);
        $record->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => Carbon::now(),
        ]);

        return redirect()->route('signage-wellness-details.index')->with('success', 'Deleted successfully.');
    }
}
