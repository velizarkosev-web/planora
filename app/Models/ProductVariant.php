<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ProductVariant extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'sale_price',
        'sale_starts_at',
        'sale_ends_at',
        'stock_quantity',
        'options',
        'is_active',
        'position',
    ];

    /**
     * Translatable attributes — the optional variant label, e.g. "Navy / Hardcover".
     */
    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',       // cents
            'sale_price' => 'integer',  // cents (nullable)
            'sale_starts_at' => 'datetime',
            'sale_ends_at' => 'datetime',
            'stock_quantity' => 'integer',
            'options' => 'array',       // {"Colour":"Navy"} ⇄ PHP array
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * A variant belongs to one product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Is this variant on sale *right now*? True only when a lower sale_price is set
     * AND the current moment falls inside the (optional) start/end window. A null
     * bound means "open-ended" on that side.
     */
    protected function isOnSale(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if ($this->sale_price === null || $this->sale_price >= $this->price) {
                    return false;
                }

                $now = now();

                if ($this->sale_starts_at !== null && $now->lt($this->sale_starts_at)) {
                    return false; // sale hasn't started yet
                }

                if ($this->sale_ends_at !== null && $now->gt($this->sale_ends_at)) {
                    return false; // sale has already ended
                }

                return true;
            },
        );
    }

    /**
     * The price the customer actually pays right now, in cents.
     */
    protected function currentPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->is_on_sale ? $this->sale_price : $this->price,
        );
    }
}
