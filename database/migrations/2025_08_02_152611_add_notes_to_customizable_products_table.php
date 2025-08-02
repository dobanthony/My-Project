<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customizable_products', function (Blueprint $table) {
            // Add a nullable text column for free-form notes, placed after allow_material
            $table->text('notes')->nullable()->after('allow_material');
        });
    }

    public function down(): void
    {
        Schema::table('customizable_products', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
