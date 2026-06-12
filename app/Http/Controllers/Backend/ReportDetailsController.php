<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\OrderDetail;

class ReportDetailsController extends Controller
{

    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'customer');
        $fromDate   = $request->input('from_date');
        $toDate     = $request->input('to_date');

        switch ($reportType) {

            /* ══════════════════════════════════════════
             |  INVENTORY
             ══════════════════════════════════════════ */
            case 'inventory':
                $products = DB::table('products_details')
                    ->select('product_name', DB::raw('SUM(quantity) as total_stock'))
                    ->groupBy('product_name')
                    ->get();

                $totals = ['total_stock' => $products->sum('total_stock')];

                return view('backend.reportdetails.index', compact('reportType', 'products', 'totals'));

            /* ══════════════════════════════════════════
             |  PRODUCT
             ══════════════════════════════════════════ */
            case 'product':
                $products  = DB::table('products_details as pd')
                    ->leftJoin('sab_category_details as sc', 'pd.sub_category_id', '=', 'sc.id')
                    ->select('pd.id', 'pd.product_name', 'sc.sab_category_name', 'pd.quantity as stock_left')
                    ->get();

                $ordersRaw = $this->getPaidOrders($fromDate, $toDate);

                foreach ($products as $product) {
                    $totalSales = $totalRevenue = 0;
                    foreach ($ordersRaw as $order) {
                        foreach ($order->product_ids as $i => $pid) {
                            if ((int)$pid === (int)$product->id) {
                                $totalSales++;
                                $totalRevenue += (float)($order->prices[$i] ?? 0);
                            }
                        }
                    }
                    $product->total_sales   = $totalSales;
                    $product->total_revenue = $totalRevenue;
                }

                $totals = [
                    'stock_left'    => $products->sum('stock_left'),
                    'total_sales'   => $products->sum('total_sales'),
                    'total_revenue' => number_format($products->sum('total_revenue'), 2),
                ];

                foreach ($products as $p) {
                    $p->total_revenue = number_format($p->total_revenue, 2);
                }

                return view('backend.reportdetails.index', compact('reportType', 'products', 'totals'));

            /* ══════════════════════════════════════════
             |  CATEGORY
             ══════════════════════════════════════════ */
            case 'category':
                [$categoryResults, $totals] = $this->buildCategoryReport($fromDate, $toDate);

                return view('backend.reportdetails.index', compact('reportType', 'categoryResults', 'totals'));

            /* ══════════════════════════════════════════
             |  OFFER
             ══════════════════════════════════════════ */
            case 'offer':
                [$offerResults, $totals] = $this->buildOfferReport($fromDate, $toDate);

                return view('backend.reportdetails.index', compact('reportType', 'offerResults', 'totals'));

            /* ══════════════════════════════════════════
             |  CUSTOMER (default)
             ══════════════════════════════════════════ */
            default:
                $customersQuery = DB::table('order_details')
                    ->select(
                        'customer_name',
                        'customer_email',
                        DB::raw('SUM(total_price) as total_spent'),
                        DB::raw('COUNT(*) as total_orders'),
                        DB::raw('MIN(created_at) as created_date')
                    )
                    ->groupBy('customer_name', 'customer_email');

                if ($fromDate) $customersQuery->whereDate('created_at', '>=', $fromDate);
                if ($toDate)   $customersQuery->whereDate('created_at', '<=', $toDate);

                $customers = $customersQuery->get();

                $totals = [
                    'total_spent'  => number_format($customers->sum('total_spent'), 2),
                    'total_orders' => $customers->sum('total_orders'),
                ];

                return view('backend.reportdetails.index', compact('reportType', 'customers', 'totals'));
        }
    }

    /* ══════════════════════════════════════════════════════
     |  EXPORT — matches whichever report is selected
     ══════════════════════════════════════════════════════ */
    public function export(Request $request)
    {
        $reportType = $request->input('report_type', 'customer');
        $fromDate   = $request->input('from_date');
        $toDate     = $request->input('to_date');

        $filename = $reportType . '_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        switch ($reportType) {

            /* ── INVENTORY ── */
            case 'inventory':
                $products = DB::table('products_details')
                    ->select('product_name', DB::raw('SUM(quantity) as total_stock'))
                    ->groupBy('product_name')
                    ->get();

                $callback = function () use ($products) {
                    $f = fopen('php://output', 'w');
                    fputcsv($f, ['#', 'Product Name', 'Total Stock']);
                    foreach ($products as $i => $row) {
                        fputcsv($f, [$i + 1, $row->product_name, $row->total_stock]);
                    }
                    fputcsv($f, ['', 'TOTAL', $products->sum('total_stock')]);
                    fclose($f);
                };
                break;

            /* ── PRODUCT ── */
            case 'product':
                $products  = DB::table('products_details as pd')
                    ->leftJoin('sab_category_details as sc', 'pd.sub_category_id', '=', 'sc.id')
                    ->select('pd.id', 'pd.product_name', 'sc.sab_category_name', 'pd.quantity as stock_left')
                    ->get();

                $ordersRaw = $this->getPaidOrders($fromDate, $toDate);

                foreach ($products as $product) {
                    $s = $r = 0;
                    foreach ($ordersRaw as $order) {
                        foreach ($order->product_ids as $i => $pid) {
                            if ((int)$pid === (int)$product->id) {
                                $s++;
                                $r += (float)($order->prices[$i] ?? 0);
                            }
                        }
                    }
                    $product->total_sales   = $s;
                    $product->total_revenue = $r;
                }

                $callback = function () use ($products) {
                    $f = fopen('php://output', 'w');
                    fputcsv($f, ['#', 'Product Name', 'Sub Category', 'Stock Left', 'Total Sales', 'Total Revenue (Rs)']);
                    foreach ($products as $i => $row) {
                        fputcsv($f, [
                            $i + 1,
                            $row->product_name,
                            $row->sab_category_name,
                            $row->stock_left,
                            $row->total_sales,
                            number_format($row->total_revenue, 2),
                        ]);
                    }
                    fputcsv($f, ['', 'TOTAL', '', $products->sum('stock_left'), $products->sum('total_sales'), number_format($products->sum('total_revenue'), 2)]);
                    fclose($f);
                };
                break;

            /* ── CATEGORY ── */
            case 'category':
                [$categoryResults] = $this->buildCategoryReport($fromDate, $toDate);

                $callback = function () use ($categoryResults) {
                    $f = fopen('php://output', 'w');
                    fputcsv($f, ['#', 'Category Name', 'Total Order Lines', 'Total Qty Sold', 'Total Amount (Rs)']);
                    foreach ($categoryResults as $i => $row) {
                        fputcsv($f, [$i + 1, $row->category_name, $row->total_orders, $row->total_qty, $row->total_amount]);
                    }
                    fputcsv($f, [
                        '', 'TOTAL',
                        $categoryResults->sum('total_orders'),
                        $categoryResults->sum('total_qty'),
                        number_format($categoryResults->sum(fn($r) => (float)str_replace(',', '', $r->total_amount)), 2),
                    ]);
                    fclose($f);
                };
                break;

            /* ── OFFER ── */
            case 'offer':
                [$offerResults] = $this->buildOfferReport($fromDate, $toDate);

                $callback = function () use ($offerResults) {
                    $f = fopen('php://output', 'w');
                    fputcsv($f, ['#', 'Offer Name', 'Offer Price (Rs)', 'Total Orders', 'Total Qty Sold', 'Total Amount (Rs)']);
                    foreach ($offerResults as $i => $row) {
                        fputcsv($f, [
                            $i + 1,
                            $row->offer_name,
                            $row->offer_price,
                            $row->total_orders,
                            $row->total_qty,
                            $row->total_amount,
                        ]);
                    }
                    fputcsv($f, [
                        '', 'TOTAL', '',
                        $offerResults->sum('total_orders'),
                        $offerResults->sum('total_qty'),
                        number_format($offerResults->sum(fn($r) => (float)str_replace(',', '', $r->total_amount)), 2),
                    ]);
                    fclose($f);
                };
                break;

            /* ── CUSTOMER (default) ── */
            default:
                $customersQuery = DB::table('order_details')
                    ->select(
                        'customer_name',
                        'customer_email',
                        DB::raw('SUM(total_price) as total_spent'),
                        DB::raw('COUNT(*) as total_orders'),
                        DB::raw('MIN(created_at) as created_date')
                    )
                    ->groupBy('customer_name', 'customer_email');

                if ($fromDate) $customersQuery->whereDate('created_at', '>=', $fromDate);
                if ($toDate)   $customersQuery->whereDate('created_at', '<=', $toDate);

                $customers = $customersQuery->get();

                $callback = function () use ($customers) {
                    $f = fopen('php://output', 'w');
                    fputcsv($f, ['#', 'Customer Name', 'Email', 'Total Amount (Rs)', 'Total Orders', 'Created Date']);
                    foreach ($customers as $i => $row) {
                        fputcsv($f, [
                            $i + 1,
                            $row->customer_name,
                            $row->customer_email,
                            number_format($row->total_spent, 2),
                            $row->total_orders,
                            Carbon::parse($row->created_date)->format('Y-m-d'),
                        ]);
                    }
                    fputcsv($f, ['', 'TOTAL', '', number_format($customers->sum('total_spent'), 2), $customers->sum('total_orders'), '']);
                    fclose($f);
                };
                break;
        }

        return response()->stream($callback, 200, $headers);
    }

    /* ══════════════════════════════════════════════════════
     |  SHARED REPORT BUILDERS
     |  (used by both index() and export() so logic never drifts)
     ══════════════════════════════════════════════════════ */

    private function buildCategoryReport(?string $fromDate, ?string $toDate): array
    {
        $categories = DB::table('category_details')
            ->whereNull('deleted_at')
            ->get();

        // Map: product_id → category_id
        $productCatMap = DB::table('products_details')
            ->select('id', 'category_id')
            ->get()
            ->pluck('category_id', 'id')
            ->map(fn($cid) => (int)$cid);

        $paidOrders = DB::table('order_details')
            ->where('payment_status', 'paid')
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->select('product_ids', 'quantities', 'prices')
            ->get()
            ->map(function ($row) {
                $row->product_ids = $this->decodeJsonColumn($row->product_ids);
                $row->quantities  = $this->decodeJsonColumn($row->quantities);
                $row->prices      = $this->decodeJsonColumn($row->prices);
                return $row;
            });

        $tally = [];
        foreach ($paidOrders as $order) {
            foreach ($order->product_ids as $i => $pid) {
                $pid   = (int)$pid;
                $catId = $productCatMap[$pid] ?? null;
                if (!$catId) continue;

                $qty   = (int)($order->quantities[$i] ?? 1);
                $price = (float)($order->prices[$i]   ?? 0);

                $tally[$catId] ??= ['orders' => 0, 'qty' => 0, 'amount' => 0.0];
                $tally[$catId]['orders']++;
                $tally[$catId]['qty']    += $qty;
                $tally[$catId]['amount'] += ($price * $qty);
            }
        }

        $results = $categories->map(function ($cat) use ($tally) {
            $t = $tally[$cat->id] ?? ['orders' => 0, 'qty' => 0, 'amount' => 0.0];
            return (object)[
                'category_name' => $cat->category_name,
                'total_orders'  => $t['orders'],
                'total_qty'     => $t['qty'],
                'total_amount'  => $t['amount'],  // raw float
            ];
        });

        $totals = [
            'total_orders' => $results->sum('total_orders'),
            'total_qty'    => $results->sum('total_qty'),
            'total_amount' => number_format($results->sum('total_amount'), 2),
        ];

        // Format after summing
        foreach ($results as $r) {
            $r->total_amount = number_format($r->total_amount, 2);
        }

        return [$results, $totals];
    }

    private function buildOfferReport(?string $fromDate, ?string $toDate): array
    {
        $offers = DB::table('offers')
            ->whereNull('deleted_at')
            ->get();

        $paidOrders = DB::table('order_details')
            ->whereIn('payment_status', ['paid', 'cod'])
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->select('offer_ids', 'quantities', 'prices')
            ->get()
            ->map(function ($row) {
                $row->offer_ids  = $this->decodeJsonColumn($row->offer_ids);
                $row->quantities = $this->decodeJsonColumn($row->quantities);
                $row->prices     = $this->decodeJsonColumn($row->prices);
                return $row;
            });

        $offerTally = [];
        foreach ($paidOrders as $order) {
            foreach ($order->offer_ids as $i => $offId) {
                $offId = (int)$offId;
                if ($offId === 0) continue;

                $qty   = (int)($order->quantities[$i] ?? 1);
                $price = (float)($order->prices[$i]   ?? 0);

                $offerTally[$offId] ??= ['orders' => 0, 'qty' => 0, 'amount' => 0.0];
                $offerTally[$offId]['orders']++;
                $offerTally[$offId]['qty']    += $qty;
                $offerTally[$offId]['amount'] += ($price * $qty);
            }
        }

        $results = $offers->map(function ($offer) use ($offerTally) {
            $t = $offerTally[$offer->id] ?? ['orders' => 0, 'qty' => 0, 'amount' => 0.0];
            return (object)[
                'offer_name'   => $offer->offer_name,
                'offer_price'  => $offer->offer_price,
                'offer_image'  => $offer->offer_image,
                'total_orders' => $t['orders'],
                'total_qty'    => $t['qty'],
                'total_amount' => $t['amount'],  // raw float
            ];
        });

        $totals = [
            'total_orders' => $results->sum('total_orders'),
            'total_qty'    => $results->sum('total_qty'),
            'total_amount' => number_format($results->sum('total_amount'), 2),
        ];

        foreach ($results as $r) {
            $r->total_amount = number_format($r->total_amount, 2);
        }

        return [$results, $totals];
    }

    /* ══════════════════════════════════════════════════════
     |  PRIVATE HELPERS
     ══════════════════════════════════════════════════════ */

    private function getPaidOrders(?string $fromDate, ?string $toDate)
    {
        return DB::table('order_details')
            ->where('payment_status', 'paid')
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->select('product_ids', 'prices')
            ->get()
            ->map(function ($row) {
                $row->product_ids = $this->decodeJsonColumn($row->product_ids);
                $row->prices      = $this->decodeJsonColumn($row->prices);
                return $row;
            });
    }

    private function decodeJsonColumn($value): array
    {
        if ($value === null || $value === '' || $value === 'NULL') {
            return [];
        }

        $value = trim($value);

        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = json_decode($value, true) ?? $value;
        }

        if (is_array($value)) return $value;

        $decoded = json_decode($value, true);

        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (is_array($decoded)) return $decoded;

        return array_map('trim', explode(',', (string)$value));
    }

    /* ══════════════════════════════════════════════════════
     |  INVOICE VIEW
     ══════════════════════════════════════════════════════ */
    public function viewInvoice($email)
    {
        $order = DB::table('order_details')
            ->where('customer_email', $email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$order) abort(404, 'No orders found for this customer.');

        $productNames = json_decode($order->product_names, true) ?? [];
        $quantities   = json_decode($order->quantities, true)    ?? [];
        $prices       = json_decode($order->prices, true)        ?? [];

        return view('backend.orders.show', compact('order', 'productNames', 'quantities', 'prices'));
    }
}