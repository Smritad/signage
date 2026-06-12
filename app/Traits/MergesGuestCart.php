<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait MergesGuestCart
{
    /**
     * Merge the guest (session-based) cart into the logged-in user's cart.
     * Call this AFTER login, passing the session id captured BEFORE regenerate().
     */
    protected function mergeGuestCart(int $userId, ?string $guestSessionId = null): void
    {
        $guestSessionId = $guestSessionId ?: session()->getId();
        if (!$guestSessionId) {
            return;
        }

        $guestRows = DB::table('carts')
            ->whereNull('user_id')
            ->where('session_id', $guestSessionId)
            ->get();

        if ($guestRows->isEmpty()) {
            return;
        }

        foreach ($guestRows as $guest) {
            // Find an existing row already owned by the user for the same item.
            $match = DB::table('carts')
                ->where('user_id', $userId)
                ->where('combo', $guest->combo)
                ->when($guest->combo === 'offer',
                    fn($q) => $q->where('offer_id', $guest->offer_id),
                    fn($q) => $q->where('product_id', $guest->product_id)
                )
                ->first();

            if ($match) {
                if ($guest->combo === 'offer') {
                    // Bundle already in user cart — just drop the duplicate guest row.
                    DB::table('carts')->where('id', $guest->id)->delete();
                } else {
                    // Same product — merge quantities, then remove the guest row.
                    DB::table('carts')->where('id', $match->id)->update([
                        'quantity'   => $match->quantity + $guest->quantity,
                        'updated_at' => now(),
                    ]);
                    DB::table('carts')->where('id', $guest->id)->delete();
                }
            } else {
                // No match — convert the guest row to belong to the user.
                DB::table('carts')->where('id', $guest->id)->update([
                    'user_id'    => $userId,
                    'session_id' => null,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}