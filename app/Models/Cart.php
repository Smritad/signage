<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'product_name',
        'slug',
        'price',
        'offer_price',
        'quantity',
        'images',
    ];

    // If you want to automatically cast images to array
    protected $casts = [
        'images' => 'array',
    ];
}
