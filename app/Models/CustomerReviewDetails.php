<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReviewDetails extends Model
{
    protected $table = 'customer_review_details';

    protected $fillable = [
        'heading',
        'items',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public $timestamps = false; // we'll manually handle timestamps
}
