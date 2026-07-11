<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * The public product detail page. Only PUBLISHED + active products are reachable —
     * a draft or hidden product 404s (the storefront side of our publish protection).
     */
    public function show(string $slug): Response
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('state', 'published')
            ->where('is_active', true)
            ->with(['category', 'defaultVariant', 'media'])
            ->firstOrFail();

        $variant = $product->defaultVariant;

        return Inertia::render('Products/Show', [
            'product' => [
                'slug' => $product->slug,
                // Both translations sent; Vue toggles bg/en client-side.
                'name' => $product->getTranslations('name'),
                'description' => $product->getTranslations('description'),
                'category' => $product->category?->getTranslations('name'),
                // Prices in integer cents — Vue formats to euros.
                'price' => $variant?->current_price,
                'regularPrice' => $variant?->price,
                'onSale' => (bool) $variant?->is_on_sale,
                'inStock' => (int) ($variant?->stock_quantity ?? 0) > 0,
                'specs' => $product->specs,
                // Ordered list of storage paths; Vue builds the Glide URLs (/img/...?w=...).
                'images' => $product->media->pluck('path')->values()->all(),
            ],
        ]);
    }
}
