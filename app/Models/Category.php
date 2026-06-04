<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    /**
     * Attributes that are mass-assignable (safe to fill from a form).
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'position',
    ];

    /**
     * Cast raw DB values to proper PHP types.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * A category has many products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
