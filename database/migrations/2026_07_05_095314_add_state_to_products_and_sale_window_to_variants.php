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
        // Editorial workflow: a product stays 'draft' (invisible on the storefront)
        // until the shop owner explicitly publishes it. String (not DB enum) so we can add
        // states later without a painful ALTER — validated at the app layer instead.
        Schema::table('products', function (Blueprint $table) {
            $table->string('state')->default('draft')->after('is_active');
        });

        // Timed sale window: the variant's sale_price only applies between these two
        // moments. Nullable = "no start/end bound". Stored as UTC timestamps; the
        // admin shows/edits them in Europe/Sofia time.
        Schema::table('product_variants', function (Blueprint $table) {
            $table->timestamp('sale_starts_at')->nullable()->after('sale_price');
            $table->timestamp('sale_ends_at')->nullable()->after('sale_starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['sale_starts_at', 'sale_ends_at']);
        });
    }
};
