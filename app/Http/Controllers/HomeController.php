<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * The storefront homepage. Loads published, active products with their
     * cheapest current price and whether any variant is on sale, plus the
     * categories. Names/descriptions are passed in BOTH locales so the page can
     * switch language client-side (a live demo of the bilingual catalog).
     *
     * The `published` + `is_active` filter mirrors the product page: a draft can
     * never surface on the storefront, only work the shop owner has deliberately released.
     */
    public function index(): Response
    {
        $products = Product::query()
            ->where('state', 'published')
            ->where('is_active', true)
            ->with([
                'category',
                'primaryMedia',
                'variants' => fn ($q) => $q->where('is_active', true),
            ])
            ->orderBy('position')
            ->get()
            ->map(function (Product $product) {
                $variants = $product->variants;

                return [
                    'slug' => $product->slug,
                    'name' => $product->getTranslations('name'),
                    'category' => $product->category->getTranslations('name'),
                    'image' => $product->primaryMedia?->path, // null → card shows the emoji fallback
                    'priceFrom' => (int) ($variants->min(fn ($v) => $v->current_price) ?? 0),
                    'onSale' => $variants->contains(fn ($v) => $v->is_on_sale),
                    'variantCount' => $variants->count(),
                    'specs' => $product->specs,
                ];
            })
            ->values();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('position')
            ->get()
            ->map(fn (Category $category) => [
                'slug' => $category->slug,
                'name' => $category->getTranslations('name'),
                'productCount' => $category->products_count,
            ])
            ->values();

        return Inertia::render('Home', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
