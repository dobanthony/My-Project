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
        Schema::table('customizable_products', function (Blueprint $table) {
            $table->boolean('allow_description')->default(false)->after('allow_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customizable_products', function (Blueprint $table) {
            $table->dropColumn('allow_description');
        });
    }
};
