<?php

use App\Filament\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

/*
|--------------------------------------------------------------------------
| Time-aware sale window  (pure model logic — the scheduled-pricing engine)
|--------------------------------------------------------------------------
*/

// ── WORKED EXAMPLE (your pattern to mirror) ─────────────────────────────
it('uses the regular price when there is no sale', function () {
    $variant = new ProductVariant(['price' => 2000]);

    expect($variant->is_on_sale)->toBeFalse()
        ->and($variant->current_price)->toBe(2000);
});

// ── 👉 YOU WRITE THE ASSERTIONS ─────────────────────────────────────────
it('is on sale when the sale price is lower and there is no time window', function () {
    $variant = new ProductVariant(['price' => 2000, 'sale_price' => 1500]);

    expect($variant->is_on_sale)->toBeTrue()
        ->and($variant->current_price)->toBe(1500);
    // 👉 assert: is_on_sale is TRUE, and current_price is 1500
});

it('is NOT on sale before the window opens', function () {
    $variant = new ProductVariant([
        'price' => 2000,
        'sale_price' => 1500,
        'sale_starts_at' => now()->addDay(), // starts tomorrow
    ]);
    
    expect($variant->is_on_sale)->toBeFalse()
        ->and($variant->current_price)->toBe(2000);
    // 👉 assert: is_on_sale is FALSE, and current_price falls back to 2000
});

it('is NOT on sale after the window closes', function () {
    $variant = new ProductVariant([
        'price' => 2000,
        'sale_price' => 1500,
        'sale_starts_at' => now()->subDays(2),
        'sale_ends_at' => now()->subDay(), // ended yesterday
    ]);

    expect($variant->is_on_sale)->toBeFalse()
        ->and($variant->current_price)->toBe(2000);

    // 👉 assert: is_on_sale is FALSE
});

/*
|--------------------------------------------------------------------------
| Money conversion  (euros in the form  ⇄  integer cents in the DB)
|--------------------------------------------------------------------------
*/

it('converts euros to integer cents', function () {
    expect(ProductResource::toCents('19.90'))->toBe(1990)
        ->and(ProductResource::toCents(null))->toBeNull();
    // 👉 assert BOTH, chained with ->and(...):
    //    ProductResource::toCents('19.90')  is  1990
    //    ProductResource::toCents(null)     is  null
});

/*
|--------------------------------------------------------------------------
| Default-variant wiring  (our helper + relationship, end to end)
|--------------------------------------------------------------------------
*/

// ── WORKED EXAMPLE ──────────────────────────────────────────────────────
it('saves the price onto a hidden default variant, in cents', function () {
    $category = Category::create([
        'name' => ['bg' => 'Тест', 'en' => 'Test'],
        'slug' => 'test',
    ]);

    // This mirrors exactly what the Create page does on save:
    $data = [
        'category_id' => $category->id,
        'name' => ['bg' => 'Продукт', 'en' => 'Product'],
        'slug' => 'produkt',
        'state' => 'draft',
        'price' => '24.90',       // entered in euros
        'stock_quantity' => '5',
    ];

    $variantData = ProductResource::extractVariantData($data); // peels off variant fields
    $product = Product::create($data);
    $product->defaultVariant()->create($variantData);

    expect($product->fresh()->defaultVariant)->not->toBeNull()
        ->and($product->defaultVariant->price)->toBe(2490) // stored as cents
        ->and($product->defaultVariant->stock_quantity)->toBe(5);
});
