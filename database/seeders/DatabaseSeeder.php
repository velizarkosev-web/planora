<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // No default test user — this is a production-bound app; we don't want a
        // predictable account created by an accidental `db:seed`. Re-enable a
        // local-only user here if a future test needs one.

        $this->call([
            CatalogSeeder::class,
        ]);
    }
}
