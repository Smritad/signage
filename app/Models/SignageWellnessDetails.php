<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignageWellnessDetails extends Model
{
    protected $table = 'signage_wellness_details';

    protected $fillable = [
        'heading',
        'items',
        'created_by',
        'modified_by',
        'modified_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public $timestamps = true;
}
