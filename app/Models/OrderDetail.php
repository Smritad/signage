<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_id',
        'transaction_id',
        'payment_status',
        'payment_method',
        'failure_reason',
        'gateway_response',
        'customer_name',
        'customer_email',
        'customer_phone',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'billing_address',
        'shipping_address',
        'description',
        'cgst',
        'sgst',
        'igst',
        'total_price',
        'status',
        'product_ids',
        'product_names',
        'quantities',
        'prices',
        'subtotals',
        'images',
        'sizes',
        'colors',
        'combo_product',
        'sub_category_ids',
        'offer_ids',
        'offer_data',
        'invoice_id',
        'order_state',
        'order_state_at',
        'is_shipped',
        'shipment_id',
        'channel_order_id',
        'awb_code',
        'courier_company_id',
        'courier_name',
        'courier_status',
        'delivery_status',
        'created_by',
        'created_at',
        'updated_at',
    ];

    // ╔══════════════════════════════════════════════════════════╗
    // ║  These columns are JSON arrays stored as text in DB.    ║
    // ║  Eloquent will auto encode/decode them on read/write.   ║
    // ║  'images' here is an ARRAY of filenames for the order   ║
    // ║  (one per line item) — different from cart.images.      ║
    // ╚══════════════════════════════════════════════════════════╝
    protected $casts = [
        'product_ids'      => 'array',
        'product_names'    => 'array',
        'quantities'       => 'array',
        'prices'           => 'array',
        'subtotals'        => 'array',
        'images'           => 'array',   // array of per-item filenames
        'sizes'            => 'array',
        'colors'           => 'array',
        'offer_ids'        => 'array',
        'offer_data'       => 'array',
        'gateway_response' => 'array',
        'total_price'      => 'float',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'order_state_at'   => 'datetime',
    ];

    /* status convention: 3 = cancelled, 5 = refunded */
    const STATUS_CANCELLED = 3;
    const STATUS_REFUNDED  = 5;

    /* Admin-managed order lifecycle states (order_state column). */
    const STATE_CANCELLED_BY_USER = 'cancelled_by_user';
    const STATE_REFUNDED          = 'refunded';
    const STATE_CLOSED            = 'closed';

    /** Human labels for each manageable order state. */
    public static function stateOptions(): array
    {
        return [
            self::STATE_CANCELLED_BY_USER => 'Cancelled by User',
            self::STATE_REFUNDED          => 'Refunded',
            self::STATE_CLOSED            => 'Closed Order',
        ];
    }

    /** Display label for the current order_state (empty string if none). */
    public function stateLabel(): string
    {
        return self::stateOptions()[$this->order_state] ?? '';
    }

    /** Has this order been cancelled (by customer or admin)? */
    public function isCancelled(): bool
    {
        // order_state is the source of truth; payment_status kept for legacy
        // admin cancels. (status tinyint is legacy/ambiguous — not used here.)
        return $this->order_state === self::STATE_CANCELLED_BY_USER
            || strtolower(trim($this->payment_status ?? '')) === 'cancelled';
    }

    /**
     * A customer may cancel only while the order is an active placed order
     * (paid / COD) AND the courier has NOT yet reached "out for delivery"
     * (or delivered / returned / cancelled).
     */
    public function isCancellable(): bool
    {
        // Already in a managed end-state (cancelled / refunded / closed).
        if (!empty($this->order_state) || $this->isCancelled()) {
            return false;
        }

        $ps = strtolower(trim($this->payment_status ?? ''));
        if (!in_array($ps, ['paid', 'cod'])) {
            return false; // only active, placed orders can be cancelled
        }

        $cs = strtolower(trim($this->courier_status ?? ''));
        foreach (['out for delivery', 'delivered', 'rto', 'return', 'returned', 'canceled', 'cancelled'] as $blocked) {
            if ($cs !== '' && str_contains($cs, $blocked)) {
                return false; // once out for delivery (or beyond), no cancel
            }
        }

        return true;
    }
}