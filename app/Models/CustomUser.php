<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;

class CustomUser extends Authenticatable
{
    use SoftDeletes, Notifiable, CanResetPassword;

    protected $table = 'custom_users';

    protected $fillable = [
    'name',
    'email',
    'password',
    'mobile_no',
    'country',
    'state',
    'city',
    'street',
    'postal_code',
    'billing_address',
    'shipping_address',
    'provider',
    'provider_id',
    'avatar',

 
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
