<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update BLs table for Supplier Info
        Schema::table('bls', function (Blueprint $table) {
            $table->string('supplier_name')->nullable(); // STE Iman / Salon Gres
            $table->string('supplier_ref')->nullable(); // Their BL Number
            $table->string('supplier_photo')->nullable(); // Path to uploaded image
        });

        // 2. Update Articles table for granular tracking
        Schema::table('articles', function (Blueprint $table) {
            $table->float('quantity_delivered')->default(0); // How much actually loaded
            $table->enum('status', ['pending', 'partial', 'delivered'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn(['supplier_name', 'supplier_ref', 'supplier_photo']);
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['quantity_delivered', 'status']);
        });
    }
};