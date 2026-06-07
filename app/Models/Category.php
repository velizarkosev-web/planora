<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;
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
     * Translatable attributes — stored as JSON ({"bg":..., "en":...}) and
     * returned in the current locale by Spatie's HasTranslations trait.
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
