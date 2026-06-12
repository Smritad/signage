<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'deleted_at',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Auto-generate slug on create / update if it is missing or empty
    // ─────────────────────────────────────────────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->sab_category_name);
            }
        });

        static::updating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->sab_category_name);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Null-safe accessor — if the DB row somehow still has no slug,
    // return a slug derived from the name instead of null.
    // This prevents "Attempt to read property slug on null" everywhere.
    // ─────────────────────────────────────────────────────────────────────────
    public function getSlugAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        // Fallback: generate from name (not persisted here — use the command below to backfill)
        return Str::slug($this->sab_category_name ?? 'subcategory');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper: generate a slug that does not already exist in the table
    // ─────────────────────────────────────────────────────────────────────────
    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base  = Str::slug($name);
        $slug  = $base;
        $count = 1;

        while (
            static::where('slug', $slug)
                  ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                  ->exists()
        ) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relationship
    // ─────────────────────────────────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(CategoryDetails::class, 'category_id');
    }
}