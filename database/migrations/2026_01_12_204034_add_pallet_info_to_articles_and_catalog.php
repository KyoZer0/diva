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
        Schema::table('articles', function (Blueprint $table) {
            $table->decimal('boxes_per_pallet', 8, 2)->nullable()->after('quantity');
            $table->integer('pallet_count')->nullable()->after('quantity');
        });

        Schema::table('catalog_products', function (Blueprint $table) {
            $table->decimal('default_boxes_per_pallet', 8, 2)->nullable()->after('default_conversion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['boxes_per_pallet', 'pallet_count']);
        });

        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropColumn('default_boxes_per_pallet');
        });
    }
};
