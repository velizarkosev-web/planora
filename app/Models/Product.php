<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /**
     * Attributes that are mass-assignable (safe to fill from a form).
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'sku',
        'stock_quantity',
        'specs',
        'is_active',
        'position',
    ];

    /**
     * Cast raw DB values to proper PHP types.
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer', // cents
            'stock_quantity' => 'integer',
            'specs' => 'array', // JSON column ⇄ PHP array
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
     * A product has many media items (gallery), ordered for display.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('position');
    }

    /**
     * Convenience: read/write the price in major units (e.g. 19.99)
     * while the database keeps it as an integer number of cents (1999).
     */
    protected function priceInMajorUnits(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): float => $attributes['price'] / 100,
            set: fn (float $value): array => ['price' => (int) round($value * 100)],
        );
    }
}
