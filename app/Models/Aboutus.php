<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aboutus extends Model
{
    use HasFactory;

    protected $table = 'aboutus';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Optional: Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? asset('signage/home/productimage/' . $this->image) 
            : asset('signage/home/productimage/default.png');
    }
}
