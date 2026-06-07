<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Seed a small, category-agnostic sample catalog to develop against.
     *
     * Uses updateOrCreate keyed on the unique slug, so the seeder is
     * idempotent — running it repeatedly updates rows instead of
     * duplicating them. Prices are in cents (e.g. 2999 = 29.99).
     *
     * Note how `specs` differs per category (a planbook has year/pages, a
     * sticker has dimensions) with no schema change — that is the
     * category-agnostic design in action.
     */
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'Planbooks',
                'slug' => 'planbooks',
                'description' => 'Yearly planning notebooks.',
                'position' => 1,
                'products' => [
                    [
                        'name' => '2026 Daily Planbook',
                        'slug' => '2026-daily-planbook',
                        'price' => 2999,
                        'stock_quantity' => 40,
                        'specs' => ['year' => 2026, 'pages' => 384, 'format' => 'A5'],
                        'media' => 'products/2026-daily-planbook/cover.jpg',
                    ],
                    [
                        'name' => '2026 Weekly Planbook',
                        'slug' => '2026-weekly-planbook',
                        'price' => 2499,
                        'stock_quantity' => 60,
                        'specs' => ['year' => 2026, 'pages' => 160, 'format' => 'A5'],
                        'media' => 'products/2026-weekly-planbook/cover.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Stickers',
                'slug' => 'stickers',
                'description' => 'Decorative and functional sticker packs.',
                'position' => 2,
                'products' => [
                    [
                        'name' => 'Productivity Sticker Pack',
                        'slug' => 'productivity-sticker-pack',
                        'price' => 799,
                        'stock_quantity' => 100,
                        'specs' => ['count' => 24, 'width_mm' => 50, 'finish' => 'matte'],
                        'media' => 'products/productivity-sticker-pack/cover.jpg',
                    ],
                ],
            ],
        ];

        foreach ($catalog as $entry) {
            $products = $entry['products'];
            unset($entry['products']);

            $category = Category::updateOrCreate(
                ['slug' => $entry['slug']],
                $entry,
            );

            foreach ($products as $data) {
                $mediaPath = $data['media'];
                unset($data['media']);

                $product = $category->products()->updateOrCreate(
                    ['slug' => $data['slug']],
                    $data,
                );

                $product->media()->updateOrCreate(
                    ['path' => $mediaPath],
                    ['type' => 'image', 'is_primary' => true, 'position' => 0],
                );
            }
        }
    }
}
