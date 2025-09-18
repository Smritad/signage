<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductsDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products_details';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'product_name',
        'price',
        'product_sku',
        'discount',
        'quantity',
        'estimate_delivery',
        'return_policy',
        'images',
        'perfume_notes',
        'perfume_note_level',
        'perfume_details',
        'fragrance_type_id',
        'measurement_unit',
                'offer_price',

        'description',
        'key_benefits',
        'how_to_use',
        'faqs',
        'created_by',
        'deleted_by',   // ✅ add this so update() works
    ];

    protected $casts = [
        'images' => 'array',
        'perfume_notes' => 'array',
        'perfume_details' => 'array',
        'faqs' => 'array',
    ];

    // 🔗 Relationships
    public function category()
    {
        return $this->belongsTo(CategoryDetails::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategoryDetails::class, 'sub_category_id');
    }

    public function fragranceType()
    {
        return $this->belongsTo(FragranceTypeDetails::class, 'fragrance_type_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by'); // ✅ relation for deleted_by
    }
}
