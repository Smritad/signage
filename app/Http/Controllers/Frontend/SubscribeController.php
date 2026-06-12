<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SubscribeController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $details = [
            'email' => $request->email
        ];

        // Send to Admin
        try {
            Mail::send('emails.subscribe_admin', ['details' => $details], function($message) use ($details) {
                $message->to('smrita@matrixbricks.com')
                        ->cc(['shweta@matrixbricks.com'])
                        ->subject('New Subscription');
            });
        } catch (\Exception $e) {
            Log::error('Subscription admin mail failed: '.$e->getMessage());
            return response()->json(['message' => 'Failed to send subscription.'], 500);
        }

        // Optional: Send Thank You Mail to User
        try {
            Mail::send('emails.subscribe_user', ['details' => $details], function($message) use ($details) {
                $message->to($details['email'])
                        ->subject('Thank You for Subscribing');
            });
        } catch (\Exception $e) {
            Log::error('Subscription user mail failed: '.$e->getMessage());
        }

        return response()->json(['message' => 'Thank you for subscribing!']);
    }
}
