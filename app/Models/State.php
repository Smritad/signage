<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'main_states';
    protected $fillable = ['name','country_id'];
    public $timestamps = false;
}
