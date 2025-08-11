<?php
namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterDetails;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FooterDetailsController extends Controller
{
    public function index()
    {
        $records = FooterDetails::latest()->get();
        return view('backend.home-page.footer-details.index', compact('records'));
    }

    public function create()
    {
        return view('backend.home-page.footer-details.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'footer_heading' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'newsletter_heading' => 'nullable|string|max:255',
            'newsletter_description' => 'nullable|string',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'twitter_link' => 'nullable|url|max:255',
        ]);


        $data = $request->all();
        $data['created_by'] = Auth::id();

        FooterDetails::create($data);

        return redirect()->route('footer-details.index')->with('message', 'Footer details added successfully!');
    }

    public function edit($id)
    {
        $record = FooterDetails::findOrFail($id);
        return view('backend.home-page.footer-details.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'footer_heading' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'newsletter_heading' => 'nullable|string|max:255',
            'newsletter_description' => 'nullable|string',
        ]);

        $record = FooterDetails::findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $record->update($data);

        return redirect()->route('footer-details.index')->with('message', 'Footer details updated successfully!');
    }

    public function destroy($id)
    {
        $record = FooterDetails::findOrFail($id);
        $record->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => Carbon::now(),
        ]);

        return redirect()->route('footer-details.index')->with('message', 'Footer details deleted successfully!');
    }
}
