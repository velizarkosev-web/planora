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
     * Is this variant currently on sale? (sale_price set and below the regular price)
     */
    protected function isOnSale(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->sale_price !== null && $this->sale_price < $this->price,
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
