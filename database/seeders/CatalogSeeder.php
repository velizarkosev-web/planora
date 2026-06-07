<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Seed a small, category-agnostic sample catalog to develop against.
     *
     * Demonstrates the Phase-1 model end to end:
     *  - translatable name/description (bg + en) via Spatie
     *  - products sold through variants (the buyable unit; price/stock live here)
     *  - a sale_price on one variant, options JSON, and a default single variant
     *  - personalization config on a product
     *  - category-specific specs with no schema change
     *
     * Idempotent: updateOrCreate is keyed on unique slug / sku. Prices are in cents.
     */
    public function run(): void
    {
        $catalog = [
            [
                'slug' => 'planbooks',
                'name' => ['bg' => 'Тефтери за планиране', 'en' => 'Planbooks'],
                'description' => ['bg' => 'Годишни тефтери за планиране.', 'en' => 'Yearly planning notebooks.'],
                'position' => 1,
                'products' => [
                    [
                        'slug' => '2026-daily-planbook',
                        'name' => ['bg' => 'Дневен тефтер 2026', 'en' => '2026 Daily Planbook'],
                        'description' => ['bg' => 'Една страница на ден.', 'en' => 'One page per day.'],
                        'specs' => ['year' => 2026, 'pages' => 384, 'format' => 'A5'],
                        'personalization' => [
                            'enabled' => true,
                            'fields' => [
                                ['key' => 'emboss_name', 'type' => 'text', 'max' => 20, 'surcharge' => 500,
                                    'label' => ['bg' => 'Име за гравиране', 'en' => 'Name to emboss']],
                            ],
                        ],
                        'media' => 'products/2026-daily-planbook/cover.jpg',
                        'variants' => [
                            ['sku' => 'PB-DAILY-2026-NV-HC', 'price' => 2999, 'stock_quantity' => 40, 'position' => 1,
                                'name' => ['bg' => 'Тъмносиньо / Твърда корица', 'en' => 'Navy / Hardcover'],
                                'options' => ['Colour' => 'Navy', 'Cover' => 'Hardcover']],
                            ['sku' => 'PB-DAILY-2026-BL-SC', 'price' => 2799, 'sale_price' => 2499, 'stock_quantity' => 25, 'position' => 2,
                                'name' => ['bg' => 'Пудра / Мека корица', 'en' => 'Blush / Softcover'],
                                'options' => ['Colour' => 'Blush', 'Cover' => 'Softcover']],
                        ],
                    ],
                    [
                        'slug' => '2026-weekly-planbook',
                        'name' => ['bg' => 'Седмичен тефтер 2026', 'en' => '2026 Weekly Planbook'],
                        'description' => ['bg' => 'Седмичен изглед.', 'en' => 'Weekly overview.'],
                        'specs' => ['year' => 2026, 'pages' => 160, 'format' => 'A5'],
                        'media' => 'products/2026-weekly-planbook/cover.jpg',
                        'variants' => [
                            // A product with no real options still gets one default variant.
                            ['sku' => 'PB-WEEK-2026', 'price' => 2499, 'stock_quantity' => 60, 'position' => 1],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'stickers',
                'name' => ['bg' => 'Стикери', 'en' => 'Stickers'],
                'description' => ['bg' => 'Декоративни и функционални стикери.', 'en' => 'Decorative and functional sticker packs.'],
                'position' => 2,
                'products' => [
                    [
                        'slug' => 'productivity-sticker-pack',
                        'name' => ['bg' => 'Стикери за продуктивност', 'en' => 'Productivity Sticker Pack'],
                        'description' => ['bg' => 'Комплект от 24 стикера.', 'en' => 'A pack of 24 stickers.'],
                        'specs' => ['count' => 24, 'width_mm' => 50, 'finish' => 'matte'],
                        'media' => 'products/productivity-sticker-pack/cover.jpg',
                        'variants' => [
                            ['sku' => 'ST-PROD-01', 'price' => 799, 'stock_quantity' => 100, 'position' => 1],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($catalog as $entry) {
            $products = $entry['products'];
            unset($entry['products']);

            $category = Category::updateOrCreate(['slug' => $entry['slug']], $entry);

            foreach ($products as $data) {
                $mediaPath = $data['media'];
                $variants = $data['variants'];
                unset($data['media'], $data['variants']);

                $product = $category->products()->updateOrCreate(['slug' => $data['slug']], $data);

                $product->media()->updateOrCreate(
                    ['path' => $mediaPath],
                    ['type' => 'image', 'is_primary' => true, 'position' => 0],
                );

                foreach ($variants as $variant) {
                    $product->variants()->updateOrCreate(['sku' => $variant['sku']], $variant);
                }
            }
        }
    }
}
