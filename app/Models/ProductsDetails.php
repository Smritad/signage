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
        'slug',
        'price',
        'product_sku',
        'discount',
        'quantity',
        'is_active',
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
        'deleted_by',
    ];

    protected $casts = [
        'images'          => 'array',
        'perfume_notes'   => 'array',
        'perfume_details' => 'array',
        'faqs'            => 'array',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relationships — your original code, unchanged
    // ─────────────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(CategoryDetails::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(\App\Models\SabCategoryDetails::class, 'sub_category_id');
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
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ─────────────────────────────────────────────────────────────────
    // NEW: Accessors added to fix "Attempt to read property slug on null"
    //
    // Why needed: sub_category_id is stored in two different formats:
    //   Old rows → plain integer:  3
    //   New rows → JSON array:     ["2"]  or  ["2","5"]
    //
    // subCategory() belongsTo breaks for JSON rows (returns null).
    // Use $product->firstSubcategory in views instead of $product->subCategory
    // whenever you need to safely read ->slug or any other property.
    // ─────────────────────────────────────────────────────────────────

    /**
     * Returns all sub-category IDs as a plain PHP array of integers,
     * regardless of whether the column holds a plain integer or a JSON array.
     *
     *   3          → [3]
     *   "3"        → [3]
     *   ["3"]      → [3]
     *   ["2","5"]  → [2, 5]
     *
     * Usage:  $product->subCategoryIds
     */
    public function getSubCategoryIdsAttribute(): array
    {
        $raw = $this->attributes['sub_category_id'] ?? null;

        if (is_null($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_map('intval', $decoded);
        }

        if (is_numeric($raw)) {
            return [(int) $raw];
        }

        return [];
    }

    /**
     * Returns the first sub-category ID as an integer, or null.
     *
     * Usage:  $product->firstSubCategoryId
     */
    public function getFirstSubCategoryIdAttribute(): ?int
    {
        return $this->sub_category_ids[0] ?? null;
    }

    /**
     * Returns the first SabCategoryDetails model safely,
     * handling both plain-integer and JSON-array storage.
     *
     * Use this instead of $product->subCategory wherever the slug
     * is needed and sub_category_id could be a JSON array.
     *
     * Usage:  $product->firstSubcategory?->slug
     */
    public function getFirstSubcategoryAttribute(): ?SabCategoryDetails
    {
        $id = $this->first_sub_category_id;

        return $id ? SabCategoryDetails::find($id) : null;
    }

    /**
     * Returns the canonical front-end URL for this product.
     * Falls back to '#' if any slug is missing or null.
     *
     * Usage:  $product->frontendUrl
     */
    public function getFrontendUrlAttribute(): string
    {
        $catSlug = optional($this->category)->slug;
        $subSlug = optional($this->firstSubcategory)->slug;

        if (!$catSlug || !$subSlug || !$this->slug) {
            return '#';
        }

        return url("{$catSlug}/{$subSlug}/{$this->slug}");
    }
    


/* Average rating (cached for the request lifecycle) */
public function getAvgRatingAttribute()
{
    if (! array_key_exists('_avg_rating', $this->attributes)) {
        $this->attributes['_avg_rating'] = \App\Models\ProductReview::where('product_id', $this->id)
            ->where('is_approved', 1)
            ->avg('rating') ?? 0;
    }
    return round($this->attributes['_avg_rating'], 1);
}

/* Number of approved reviews */
public function getReviewCountAttribute()
{
    if (! array_key_exists('_review_count', $this->attributes)) {
        $this->attributes['_review_count'] = \App\Models\ProductReview::where('product_id', $this->id)
            ->where('is_approved', 1)
            ->count();
    }
    return (int) $this->attributes['_review_count'];
}
}