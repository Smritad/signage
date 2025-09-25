<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'main_countries';
    protected $fillable = ['shortname','name','phonecode'];
    public $timestamps = false;
}

