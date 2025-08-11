<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FooterDetails extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'footer_heading',
        'address_line1',
        'address_line2',
        'phone',
        'email',
        'newsletter_heading',
        'newsletter_description',
        'facebook_link',
        'instagram_link',
        'twitter_link',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
