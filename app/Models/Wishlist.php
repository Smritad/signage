<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';

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

    protected $casts = [
        'images' => 'array', // Automatically decode JSON
    ];
}
