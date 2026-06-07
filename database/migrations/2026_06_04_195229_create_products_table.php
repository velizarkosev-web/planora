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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->json('name');               // translatable: {"bg":..., "en":...}
            $table->string('slug')->unique();   // single canonical slug (not translated)
            $table->json('description')->nullable(); // translatable
            $table->json('specs')->nullable();  // category-specific specs, e.g. {"year":2026}
            $table->json('personalization')->nullable(); // config of customisable fields
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
            // Note: price, sku and stock now live on product_variants (the buyable unit).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
