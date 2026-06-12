<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = 'order_status_details';

    protected $fillable = [

        'user_id',
        'order_id',

        'order_status',
'payment_mode',
        'payment_status',
        'payment_method',

        'payment_id',
        'transaction_id',

        'paid_amount',

        'gateway_response',

        'status_updated_by',
        'status_updated_at',

        'order_remarks',
        'delivery_date',

        'awb_code',
        'courier_name',
        'courier_status',

        'created_at',
        'updated_at',
    ];

    protected $casts = [

        'paid_amount'       => 'float',

        'gateway_response'  => 'array',

        'delivery_date'     => 'date',

        'status_updated_at' => 'datetime',

        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];
}