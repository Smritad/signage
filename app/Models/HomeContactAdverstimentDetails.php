<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContactAdverstimentDetails extends Model
{
    protected $table = 'home_contact_adverstiment_details';

    protected $fillable = [
        'title',
        'advertisement_heading',
        'advertisement_image',
        'created_by',
        'modified_by',
        'modified_at',
        'deleted_by',
        'deleted_at',
    ];

    public $timestamps = false;
}
