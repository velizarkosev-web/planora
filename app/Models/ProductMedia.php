<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    /**
     * Eloquent would guess "product_medias"; pin it to the real table name.
     */
    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'type',
        'path',
        'alt_text',
        'is_primary',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * A media item belongs to one product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
