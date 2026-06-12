<?php
namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnPolicyDetail;

class ReturnPolicyDetailsController extends Controller
{
    public function index()
    {
        $records = ReturnPolicyDetail::latest()->get();
        return view('backend.Policies.return-policy.index', compact('records'));
    }

    public function create()
    {
        return view('backend.Policies.return-policy.create');
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
            $image->move(public_path('uploads/return_policy'), $bannerImageName);
        }

        ReturnPolicyDetail::create([
            'title' => $request->title,
            'banner_image' => $bannerImageName,
            'description' => $request->description,
        ]);

        return redirect()->route('manage-return-policy.index')->with('message', 'Return Policy added successfully!');
    }

    public function edit($id)
    {
        $record = ReturnPolicyDetail::findOrFail($id);
        return view('backend.Policies.return-policy.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = ReturnPolicyDetail::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'banner_image' => 'nullable|image',
            'description' => 'required|string',
        ]);

        $bannerImageName = $record->banner_image;
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $bannerImageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/return_policy'), $bannerImageName);
        }

        $record->update([
            'title' => $request->title,
            'banner_image' => $bannerImageName,
            'description' => $request->description,
        ]);

        return redirect()->route('manage-return-policy.index')->with('message', 'Return Policy updated successfully!');
    }

    public function destroy($id)
    {
        $record = ReturnPolicyDetail::findOrFail($id);
        $record->delete();
        return redirect()->route('manage-return-policy.index')->with('message', 'Return Policy deleted successfully!');
    }
}
