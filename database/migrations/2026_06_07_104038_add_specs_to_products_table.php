<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Flexible, category-specific specs (e.g. {"year":2026,"pages":160}
            // for a planbook, {"width_mm":50} for a sticker) — keeps the schema
            // category-agnostic without a column per attribute.
            $table->json('specs')->nullable()->after('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('specs');
        });
    }
};
