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
        'offer_id',        // ★ was missing — caused offer_id to be silently dropped on Eloquent create()
        'product_name',
        'slug',
        'price',
        'offer_price',
        'quantity',
        'images',          // ★ NO cast — store as plain string (filename or JSON array string)
        'combo',
        'combo_text',
    ];

    // ╔══════════════════════════════════════════════════════╗
    // ║  DO NOT cast 'images' to 'array'.                   ║
    // ║  Casting causes Eloquent to JSON-encode the value   ║
    // ║  on every save, turning "file.png" into             ║
    // ║  "\"file.png\"" (double-encoded, breaks img URLs).  ║
    // ╚══════════════════════════════════════════════════════╝
    protected $casts = [
        // intentionally empty for images
    ];
}