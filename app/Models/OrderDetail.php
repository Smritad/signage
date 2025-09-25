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
        'invoice_id',
        'created_at',
        'created_by'
    ];

    protected $casts = [
        'product_ids'   => 'array',
        'product_names' => 'array',
        'quantities'    => 'array',
        'prices'        => 'array',
        'subtotals'     => 'array',
        'images'        => 'array',
        'sizes'         => 'array',
        'colors'        => 'array'
    ];
}
