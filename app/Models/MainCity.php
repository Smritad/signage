<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCity extends Model
{
    protected $table = 'main_cities';

    protected $fillable = [
        'name',
        'state_id',
    ];

    public $timestamps = false; // remove if you have created_at/updated_at
}
