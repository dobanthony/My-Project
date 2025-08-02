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
        Schema::table('product_customizations', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->nullable()->after('cart_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_customizations', function (Blueprint $table) {
            $table->dropColumn('order_item_id');
        });
    }
};
