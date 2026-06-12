<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsConditions extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'banner_image', 'description'];
}
