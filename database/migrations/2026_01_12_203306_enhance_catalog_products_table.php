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
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('name');
            $table->string('unit')->default('box')->after('reference');
            $table->string('default_warehouse')->nullable()->after('unit');
            $table->decimal('default_conversion', 8, 3)->nullable()->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropColumn(['reference', 'unit', 'default_warehouse', 'default_conversion']);
        });
    }
};
