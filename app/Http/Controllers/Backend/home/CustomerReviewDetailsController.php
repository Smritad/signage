<?php
namespace App\Http\Controllers\Backend\Home;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\CustomerReviewDetails;
use Illuminate\Support\Facades\Auth;

class CustomerReviewDetailsController extends Controller
{
    public function index()
    {
        $records = CustomerReviewDetails::latest()->get();
        return view('backend.home-page.customer-review-details.index', compact('records'));
    }

    public function create()
    {
        return view('backend.home-page.customer-review-details.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'required|string',
            'items.*.name' => 'required|string|max:255',
            'items.*.rating' => 'required|numeric|min:1|max:5',
        ]);

        $items = array_map(function ($item) {
            return [
                'title' => $item['title'],
                'description' => $item['description'],
                'name' => $item['name'],
                'rating' => $item['rating'],
            ];
        }, $request->items);

        CustomerReviewDetails::create([
            'heading' => $request->heading,
            'items' => json_encode($items),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('customer-review-details.index')->with('success', 'Customer Review added successfully.');
    }

    public function edit($id)
    {
        $record = CustomerReviewDetails::findOrFail($id);
        return view('backend.home-page.customer-review-details.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'required|string',
            'items.*.name' => 'required|string|max:255',
            'items.*.rating' => 'required|numeric|min:1|max:5',
        ]);

        $record = CustomerReviewDetails::findOrFail($id);

        $items = array_map(function ($item) {
            return [
                'title' => $item['title'],
                'description' => $item['description'],
                'name' => $item['name'],
                'rating' => $item['rating'],
            ];
        }, $request->items);

        $record->update([
            'heading' => $request->heading,
            'items' => json_encode($items),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('customer-review-details.index')->with('success', 'Customer Review updated successfully.');
    }

   public function destroy($id)
{
    $record = CustomerReviewDetails::findOrFail($id);

    // Set deleted_by and soft delete (set deleted_at timestamp)
    $record->deleted_by = Auth::id();
    $record->save();

    // Soft delete - sets deleted_at timestamp, does NOT remove record
    $record->delete();

    return redirect()->route('customer-review-details.index')->with('success', 'Customer Review deleted successfully.');
}

}
