<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateShiprocketStatus extends Command
{
    protected $signature   = 'shiprocket:update-status';
    protected $description = 'Sync all Shiprocket dashboard actions back to order_details DB';

    private string $srEmail    = 'shweta@matrixbricks.com';
    private string $srPassword = 'Dz1AkDSNn6Z^e2$A';
    private string $srBaseUrl  = 'https://apiv2.shiprocket.in/v1/external';

    public function handle()
    {
        $this->info('Authenticating with Shiprocket...');

        $auth = Http::post("{$this->srBaseUrl}/auth/login", [
            'email'    => $this->srEmail,
            'password' => $this->srPassword,
        ]);

        if ($auth->failed() || empty($auth->json()['token'])) {
            $this->error('Shiprocket auth failed.');
            Log::error('[Shiprocket Cron] Auth failed', ['body' => $auth->body()]);
            return Command::FAILURE;
        }

        $token = $auth->json()['token'];
        $this->info('Authenticated.');

        $orders = DB::table('order_details')
            ->where('is_shipped', 1)
            ->whereNotNull('shipment_id')
            ->where('shipment_id', '!=', '')
            ->whereNotIn(DB::raw('LOWER(COALESCE(courier_status, ""))'), ['cancelled', 'canceled'])
            ->get();

        $this->info("Found {$orders->count()} order(s) to sync.");

        foreach ($orders as $order) {
            $this->line("Syncing Order: {$order->order_id} | Shipment: {$order->shipment_id}");

            $trackResp = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->get("{$this->srBaseUrl}/courier/track/shipment/{$order->shipment_id}");

            if ($trackResp->failed()) {
                $this->warn("  Tracking API failed for {$order->shipment_id}");
                continue;
            }

            $data     = $trackResp->json();
            $tracking = $data['tracking_data'] ?? null;

            if (!$tracking) {
                $this->warn("  No tracking_data in response.");
                continue;
            }

            $shipmentTrack = $tracking['shipment_track'][0] ?? [];

            $updateData = [
                'awb_code'           => $shipmentTrack['awb_code']           ?? $order->awb_code,
                'courier_company_id' => $shipmentTrack['courier_company_id'] ?? $order->courier_company_id,
                'courier_name'       => $shipmentTrack['courier_name']       ?? $order->courier_name,
                'courier_status'     => $shipmentTrack['current_status']     ?? $order->courier_status,
                'delivery_status'    => $tracking['shipment_status']         ?? $order->delivery_status,
                'channel_order_id'   => $tracking['channel_order_id']        ?? $order->channel_order_id,
                'updated_at'         => now(),
            ];

            $currentStatus = strtolower(trim($updateData['courier_status'] ?? ''));
            $paymentMethod = strtolower(trim($order->payment_method ?? ''));
            $paymentStatus = strtolower(trim($order->payment_status ?? ''));

            /* COD-specific logic */
            if ($paymentMethod === 'cod') {

                if ($currentStatus === 'delivered') {
                    $updateData['delivery_status'] = 'DELIVERED';
                    $this->info("  COD delivered. Checking remittance...");

                    /* Auto-mark paid if remittance confirmed */
                    if ($paymentStatus === 'cod') {
                        $remitted = $this->checkRemittance($token, $order->awb_code ?? '');
                        if ($remitted) {
                            $updateData['payment_status'] = 'paid';
                            $updateData['payment_id']     = 'COD-REMIT-AUTO-' . now()->format('YmdHis');
                            $this->info("  COD remittance confirmed - marked PAID.");
                            Log::info('[Shiprocket Cron] COD auto-paid', ['order_id' => $order->order_id]);
                        } else {
                            $this->info("  Remittance not yet received (normal - ~8 days after delivery).");
                        }
                    }
                }

                if (str_contains($currentStatus, 'rto') || str_contains($currentStatus, 'return')) {
                    $updateData['delivery_status'] = 'RTO';
                    $this->warn("  RTO detected for {$order->order_id}");
                }
            }

            DB::table('order_details')->where('id', $order->id)->update($updateData);

            $this->info("  Done - Status: {$updateData['courier_status']} | AWB: {$updateData['awb_code']}");

            Log::info('[Shiprocket Cron] Synced', [
                'order_id'        => $order->order_id,
                'courier_status'  => $updateData['courier_status'],
                'awb_code'        => $updateData['awb_code'],
                'delivery_status' => $updateData['delivery_status'],
            ]);
        }

        $this->info('All done.');
        return Command::SUCCESS;
    }

    private function checkRemittance(string $token, string $awbCode): bool
    {
        if (empty($awbCode)) return false;

        $response = Http::withToken($token)
            ->get("{$this->srBaseUrl}/account/details/cod-remittance");

        if ($response->failed()) return false;

        foreach ($response->json()['data'] ?? [] as $remit) {
            $awbList = $remit['awb_list'] ?? $remit['awbs'] ?? [];
            if (is_string($awbList)) {
                $awbList = array_map('trim', explode(',', $awbList));
            }
            if (in_array($awbCode, (array)$awbList)) {
                return in_array(strtolower($remit['status'] ?? ''), ['paid', 'success']);
            }
        }

        return false;
    }
}