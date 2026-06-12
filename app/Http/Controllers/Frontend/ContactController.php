<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
 use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\FooterDetails;

class ContactController extends Controller
{
    public function contactus()
    {
        $footer = FooterDetails::first();
        return view('frontend.contactus', compact('footer'));
    }

  



public function sendContact(Request $request)
{
    // Validation
    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email',
        'contact' => 'required|string|max:20',
        'company' => 'nullable|string|max:255',
        'message' => 'string',
    ]);

    $details = [
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->contact,
        'company' => $request->company ?? '',
        'message' => $request->message ?? 'N/A',
    ];

    // Send Admin Mail
    try {
        Mail::send('emails.contact_admin', ['details' => $details], function ($message) use ($details) {
            $message->to('smrita@matrixbricks.com')
                        ->cc(['shweta@matrixbricks.com']) // Add CC recipients here
                    ->subject('Contact Us Enquiry');
        });
    } catch (\Exception $e) {
        Log::error('Admin mail error: '.$e->getMessage());
    }

    // Send User Thank You Mail
    try {
        Mail::send('emails.contact_user', ['details' => $details], function ($message) use ($details) {
            $message->to($details['email'])
                    ->subject('Thank You for Your Enquiry');
        });
    } catch (\Exception $e) {
        Log::error('User mail error: '.$e->getMessage());
    }

    return redirect()->route('Thank.you');
}



}
