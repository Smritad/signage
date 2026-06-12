<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Offer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $filter = $request->input('filter', 'last_year');
        $now    = Carbon::now();

        $fromDate = match ($filter) {
            'today'      => $now->copy()->startOfDay(),
            'last_week'  => $now->copy()->subWeek(),
            'last_month' => $now->copy()->subMonth(),
            default      => $now->copy()->subYear(),
        };

        // --- Stat Cards (all-time) ---
        // Revenue = paid (online) + cod both counted
        $totalRevenue = OrderDetail::whereIn('payment_status', ['paid', 'cod'])->sum('total_price');
        $totalOrders  = OrderDetail::count();
        $paidOrders   = OrderDetail::where('payment_status', 'paid')->count();   // online only
        $codOrders    = OrderDetail::where('payment_status', 'cod')->count();    // cod only
        $failedOrders = OrderDetail::where('payment_status', 'failed')->count();
        $activeOffers = Offer::where('is_active', 1)->count();

        // --- Monthly Chart Data (filtered range) ---
        $months = collect(range(1, 12))->mapWithKeys(function ($m) {
            return [Carbon::create()->month($m)->format('M') => 0];
        });

        // All orders in range for order-count chart
        $allOrders = OrderDetail::where('created_at', '>=', $fromDate)
            ->orderBy('created_at', 'asc')
            ->get();

        // Only paid + cod for revenue chart
        $revenueOrders = $allOrders->whereIn('payment_status', ['paid', 'cod']);

        $salesData = $months->merge(
            $revenueOrders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('M'))
                          ->map(fn($g) => $g->sum('total_price'))
        );

        $orderCounts = $months->merge(
            $allOrders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('M'))
                      ->map(fn($g) => $g->count())
        );

        // --- Offer Revenue Analytics (all-time: paid + cod) ---
        $offerRevenue = [];

        OrderDetail::whereIn('payment_status', ['paid', 'cod'])
            ->whereNotNull('offer_ids')
            ->get(['offer_ids', 'prices'])
            ->each(function ($order) use (&$offerRevenue) {
                $offerIds = json_decode($order->offer_ids, true) ?? [];
                $prices   = json_decode($order->prices, true)   ?? [];
                foreach ($offerIds as $i => $oid) {
                    if ((int)$oid > 0) {
                        $offerRevenue[$oid] = ($offerRevenue[$oid] ?? 0) + (float)($prices[$i] ?? 0);
                    }
                }
            });

        arsort($offerRevenue);

        $offerNames   = [];
        $offerAmounts = [];

        if (!empty($offerRevenue)) {
            $offerModels = Offer::whereIn('id', array_keys($offerRevenue))
                                ->pluck('offer_name', 'id');
            foreach ($offerRevenue as $oid => $amount) {
                $offerNames[]   = $offerModels[$oid] ?? ('Offer #' . $oid);
                $offerAmounts[] = round($amount, 2);
            }
        }

        // --- Recent Orders ---
        $recentOrders = OrderDetail::orderBy('created_at', 'desc')->take(10)->get();

        return view('backend.dashboard', compact(
            'filter',
            'totalRevenue', 'totalOrders', 'paidOrders',
            'codOrders', 'failedOrders', 'activeOffers',
            'salesData', 'orderCounts',
            'offerNames', 'offerAmounts',
            'recentOrders'
        ));
    }
}
