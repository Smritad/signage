<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SabCategoryDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sab_category_details';

    protected $fillable = [
        'category_id',
        'sab_category_name',
        'slug',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryDetails::class, 'category_id');
    }
}
