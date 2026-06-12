<?php

namespace App\Http\Controllers\Backend\stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Models\User;

use App\Models\ProductsDetails;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\PerfumeNotesDetails;
use App\Models\PerfumeNotesLevelDetails;
use App\Models\FragranceTypeDetails;

class StockDetailsController extends Controller
{

    public function index()
    {

        $products = ProductsDetails::whereNull('deleted_at')->wherenotNull('quantity')->select('id', 'product_name', 'quantity','is_active')->get();
        return view('backend.stock.index', compact('products'));
    }

    public function create(Request $request)
    { 
        $products = ProductsDetails::whereNull('deleted_at')->select('id', 'product_name')->get();
        return view('backend.stock.create', compact('products'));
    }

    public function store(Request $request)
{
    $request->validate([
        'product' => 'required|exists:products_details,id',
        'quantity' => 'required|integer|min:0'
    ]);

    $product = ProductsDetails::findOrFail($request->product);

    $product->quantity = $request->quantity; // ✅ correct column
    $product->save();

    return redirect()->route('stock-details.index')->with('message', 'Stock updated successfully.');
}


    public function edit($id)
    {
        $product = ProductsDetails::findOrFail($id);

        return view('backend.stock.edit', compact('product'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:0'
    ]);

    $product = ProductsDetails::findOrFail($id);
    $product->quantity = $request->quantity; // ✅ correct column
    $product->save();

    return redirect()->route('stock-details.index')->with('message', 'Stock updated successfully.');
}

public function toggleStatus($id)
{
    $product = ProductsDetails::findOrFail($id);
    $product->is_active = !$product->is_active; // toggle between 1 and 0
    $product->save();

    return redirect()->route('stock-details.index')->with('message', 'Product status updated successfully.');
}


}