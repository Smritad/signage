<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FragranceTypeDetails extends Model
{
    use SoftDeletes;

    protected $table = 'fragrance_type_details';

    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
