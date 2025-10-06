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
        Schema::create('customizable_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->boolean('allow_color')->default(false);
            $table->boolean('allow_size')->default(false);
            $table->boolean('allow_material')->default(false);
            $table->boolean('allow_pattern')->default(false);

            $table->json('custom_options')->nullable();
            // Example JSON structure:
            // [
            //   { 
            //     "material": "Bamboo",
            //     "image": "bamboo.jpg",
            //     "colors": [{"name": "Blue", "image": "blue.jpg"}],
            //     "patterns": [{"name": "Polka Dots", "image": "dots.jpg"}],
            //     "sizes": [{"name": "Large", "image": "large.jpg"}]
            //   }
            // ]

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customizable_products');
    }
};
