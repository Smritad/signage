<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'main_cities';
    protected $fillable = ['name','state_id'];
    public $timestamps = false;
}