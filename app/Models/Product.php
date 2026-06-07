<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;
    use SoftDeletes;

    /**
     * Attributes that are mass-assignable (safe to fill from a form).
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'specs',
        'personalization',
        'is_active',
        'position',
    ];

    /**
     * Translatable attributes — stored as JSON ({"bg":..., "en":...}).
     */
    public array $translatable = [
        'name',
        'description',
    ];

    /**
     * Cast raw DB values to proper PHP types.
     */
    protected function casts(): array
    {
        return [
            'specs' => 'array',           // category-specific specs ⇄ PHP array
            'personalization' => 'array', // customisable-field config ⇄ PHP array
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * A product belongs to one category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A product is sold through one or more variants (the buyable units).
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('position');
    }

    /**
     * A product has many media items (gallery), ordered for display.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('position');
    }
}
