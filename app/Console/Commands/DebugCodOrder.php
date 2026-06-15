<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugCodOrder extends Command
{
    protected $signature   = 'debug:cod-order {order_id?}';
    protected $description = 'Check if COD orders have correct payment_method and payment_status';

    public function handle()
    {
        $orderId = $this->argument('order_id');

        if ($orderId) {
            $orders = DB::table('order_details')->where('order_id', $orderId)->get();
        } else {
            /* Show all COD orders */
            $orders = DB::table('order_details')
                ->where(function ($q) {
                    $q->where('payment_method', 'cod')
                      ->orWhere('payment_status', 'cod');
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($orders->isEmpty()) {
            $this->warn("No COD orders found.");
            return Command::SUCCESS;
        }

        $this->info("Found {$orders->count()} COD order(s):\n");

        foreach ($orders as $o) {
            $this->line(str_repeat('─', 70));
            $this->info("Order ID:        {$o->order_id}");
            $this->line("Customer:        {$o->customer_name}");
            $this->line("Total:           ₹{$o->total_price}");
            $this->line("Created:         {$o->created_at}");
            $this->newLine();

            /* Critical fields for COD shipping */
            $pmColor = ($o->payment_method === 'cod') ? '<info>' : '<comment>';
            $psColor = ($o->payment_status === 'cod') ? '<info>' : '<comment>';

            $this->line("payment_method:  {$pmColor}{$o->payment_method}</>");
            $this->line("payment_status:  {$psColor}{$o->payment_status}</>");
            $this->line("is_shipped:      " . ($o->is_shipped ? 'YES' : 'no'));
            $this->line("shipment_id:     " . ($o->shipment_id ?? '-'));
            $this->line("awb_code:        " . ($o->awb_code ?? '-'));
            $this->line("courier_status:  " . ($o->courier_status ?? '-'));
            $this->newLine();

            /* Diagnosis */
            if ($o->payment_method !== 'cod') {
                $this->error("⚠️  PROBLEM: payment_method should be 'cod' but is '{$o->payment_method}'");
                $this->warn("   → This order will be sent to Shiprocket as PREPAID, not COD!");
                $this->warn("   → Fix SQL: UPDATE order_details SET payment_method='cod' WHERE order_id='{$o->order_id}';");
            } else {
                $this->info("✅ Will ship to Shiprocket as: COD");
            }
        }

        $this->line(str_repeat('─', 70));
        return Command::SUCCESS;
    }
}