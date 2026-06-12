<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Aboutus;

class AboutController extends Controller
{
    public function index()
    {
        // Get the first active About Us record
        $about = Aboutus::first(); // You can add ->whereNull('deleted_at') if soft deletes are used

        return view('frontend.about_us', compact('about'));
    }
}
