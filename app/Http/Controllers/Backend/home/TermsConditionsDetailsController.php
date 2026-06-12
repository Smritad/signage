<?php
namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermsConditions;

class TermsConditionsDetailsController extends Controller
{
    public function index()
    {
        $records = TermsConditions::latest()->get();
        return view('backend.Policies.terms-conditions.index', compact('records'));
    }

    public function create()
    {
        return view('backend.Policies.terms-conditions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'banner_image' => 'nullable|image',
            'description' => 'required|string',
        ]);

        $bannerImageName = null;
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $bannerImageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/termsconditions_policy'), $bannerImageName);
        }

        TermsConditions::create([
            'title' => $request->title,
            'banner_image' => $bannerImageName,
            'description' => $request->description,
        ]);

        return redirect()->route('manage-terms-conditions.index')->with('message', 'Terms Conditions added successfully!');
    }

    public function edit($id)
    {
        $record = TermsConditions::findOrFail($id);
        return view('backend.Policies.terms-conditions.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = TermsConditions::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'banner_image' => 'nullable|image',
            'description' => 'required|string',
        ]);

        $bannerImageName = $record->banner_image;
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $bannerImageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/termsconditions_policy'), $bannerImageName);
        }

        $record->update([
            'title' => $request->title,
            'banner_image' => $bannerImageName,
            'description' => $request->description,
        ]);

        return redirect()->route('manage-terms-conditions.index')->with('message', 'Terms Conditions updated successfully!');
    }

    public function destroy($id)
    {
        $record = TermsConditions::findOrFail($id);
        $record->delete();
        return redirect()->route('manage-terms-conditions.index')->with('message', 'Terms Conditions deleted successfully!');
    }
}
