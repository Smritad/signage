<?php
namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicyDetail;

class PrivacyPolicyDetailsController extends Controller
{
    public function index()
    {
        $records = PrivacyPolicyDetail::latest()->get();
        return view('backend.Policies.privacy-policy.index', compact('records'));
    }

    public function create()
    {
        return view('backend.Policies.privacy-policy.create');
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
            $image->move(public_path('uploads/privacy_policy'), $bannerImageName);
        }

        PrivacyPolicyDetail::create([
            'title' => $request->title,
            'banner_image' => $bannerImageName,
            'description' => $request->description,
        ]);

        return redirect()->route('manage-privacy-policy.index')->with('message', 'Return Policy added successfully!');
    }

    public function edit($id)
    {
        $record = PrivacyPolicyDetail::findOrFail($id);
        return view('backend.Policies.privacy-policy.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = PrivacyPolicyDetail::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'banner_image' => 'nullable|image',
            'description' => 'required|string',
        ]);

        $bannerImageName = $record->banner_image;
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $bannerImageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/privacy_policy'), $bannerImageName);
        }

        $record->update([
            'title' => $request->title,
            'banner_image' => $bannerImageName,
            'description' => $request->description,
        ]);

        return redirect()->route('manage-privacy-policy.index')->with('message', 'Return Policy updated successfully!');
    }

    public function destroy($id)
    {
        $record = PrivacyPolicyDetail::findOrFail($id);
        $record->delete();
        return redirect()->route('manage-privacy-policy.index')->with('message', 'Return Policy deleted successfully!');
    }
}
