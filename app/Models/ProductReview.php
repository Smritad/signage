<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'content',
        'reviewer_name',
        'reviewer_email',
        'media',
        'is_approved',
    ];

    protected $casts = [
        'media'       => 'array',
        'is_approved' => 'boolean',
        'rating'      => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(ProductsDetails::class, 'product_id');
    }
}