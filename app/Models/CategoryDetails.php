<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category_details';

    protected $fillable = [
        'category_name',
        'image',
        'slug',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // In CategoryDetails.php
public function products()
{
    return $this->hasMany(\App\Models\ProductsDetails::class, 'category_id', 'id');
}

}
