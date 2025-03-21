<?php

use App\Models\Attribute;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductVariant::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Attribute::class)->constrained()->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
            
            // evitar duplicaciones
            $table->unique(['product_variant_id', 'attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_variants');
    }
};
