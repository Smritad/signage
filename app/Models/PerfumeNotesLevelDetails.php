<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfumeNotesLevelDetails extends Model
{
    use SoftDeletes;

    protected $table = 'perfume_notes_level_details';

    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
