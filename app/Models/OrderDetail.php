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
    ];
}