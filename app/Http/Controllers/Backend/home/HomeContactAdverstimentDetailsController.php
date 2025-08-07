<?php

namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\HomeContactAdverstimentDetails;

class HomeContactAdverstimentDetailsController extends Controller
{
    public function index()
    {
        $advertisements = HomeContactAdverstimentDetails::whereNull('deleted_at')->orderByDesc('id')->get();
        return view('backend.home-page.contact-adverstiment-details.index', compact('advertisements'));
    }

    public function create()
    {
        return view('backend.home-page.contact-adverstiment-details.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'advertisement_heading' => 'required|string|max:255',
            'advertisement_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['title', 'advertisement_heading']);
        $data['created_by'] = Auth::id();

        if ($request->hasFile('advertisement_image')) {
            $file = $request->file('advertisement_image');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/home/advertisement'), $filename);
            $data['advertisement_image'] = $filename;
        }

        HomeContactAdverstimentDetails::create($data);

        return redirect()->route('contact-adverstiment-details.index')->with('message', 'Advertisement added successfully!');
    }

    public function edit($id)
    {
        $advertisement = HomeContactAdverstimentDetails::findOrFail($id);
        return view('backend.home-page.contact-adverstiment-details.edit', compact('advertisement'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'advertisement_heading' => 'required|string|max:255',
            'advertisement_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $advertisement = HomeContactAdverstimentDetails::findOrFail($id);
        $data = $request->only(['title', 'advertisement_heading']);
        $data['modified_by'] = Auth::id();
        $data['modified_at'] = Carbon::now();

        if ($request->hasFile('advertisement_image')) {
            $file = $request->file('advertisement_image');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/home/advertisement'), $filename);
            $data['advertisement_image'] = $filename;
        }

        $advertisement->update($data);

        return redirect()->route('contact-adverstiment-details.index')->with('message', 'Advertisement updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $advertisement = HomeContactAdverstimentDetails::findOrFail($id);
            $advertisement->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()->route('contact-adverstiment-details.index')->with('message', 'Advertisement deleted successfully!');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $ex->getMessage());
        }
    }
}
