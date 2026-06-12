<?php

namespace App\Http\Controllers\Backend\footers;

use App\Http\Controllers\Controller;
use App\Models\Aboutus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AboutusController extends Controller
{
    public function index()
    {
        $abouts = Aboutus::all();
        return view('backend.footer-pages.abouts_us.index', compact('abouts'));
    }

    public function create()
    {
        return view('backend.footer-pages.abouts_us.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('signage/home/productimage'), $imageName);
        }

        Aboutus::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,
            'image'       => $imageName,
            'created_by'  => auth()->id(),
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->route('aboutus-details.index')
                         ->with('success', 'About Us entry created successfully.');
    }

    public function edit($id)
    {
        $about = Aboutus::findOrFail($id);
        return view('backend.footer-pages.abouts_us.edit', compact('about'));
    }
    
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $about = Aboutus::findOrFail($id);

        $imageName = $about->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($imageName && file_exists(public_path('signage/home/productimage/'.$imageName))) {
                unlink(public_path('signage/home/productimage/'.$imageName));
            }

            $file = $request->file('image');
            $imageName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('signage/home/productimage'), $imageName);
        }

        $about->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,
            'image'       => $imageName,
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->route('aboutus-details.index')
                         ->with('success', 'About Us entry updated successfully.');
    }

    public function destroy($id)
    {
        $about = Aboutus::findOrFail($id);

        // Delete image if exists
        if ($about->image && file_exists(public_path('signage/home/productimage/'.$about->image))) {
            unlink(public_path('signage/home/productimage/'.$about->image));
        }

        $about->delete();

        return redirect()->route('aboutus-details.index')
                         ->with('success', 'About Us entry deleted successfully.');
    }
}
