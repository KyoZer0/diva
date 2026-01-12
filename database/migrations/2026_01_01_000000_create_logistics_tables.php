<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Delivery Notes (BLs)
        Schema::create('bls', function (Blueprint $table) {
            $table->id();
            $table->string('bl_number')->unique();
            $table->string('client_name');
            $table->date('date');
            $table->enum('status', ['loading', 'loaded', 'delivered', 'returned'])->default('loading');
            $table->timestamps();
        });

        // 2. Smart Catalog (For Autocomplete)
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 3. Articles (Items inside a BL)
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bl_id')->constrained('bls')->onDelete('cascade');
            $table->string('name'); // Linked logically to catalog, but stored as string for history
            $table->string('reference')->nullable();
            $table->float('quantity');
            $table->string('unit')->default('m2'); // m2, box, pcs, kg
            $table->timestamps();
        });

        // 4. Incidents (Broken Items)
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bl_id')->nullable()->constrained('bls')->onDelete('set null'); // Null = Stock Incident
            $table->string('article_name');
            $table->float('quantity');
            $table->text('notes')->nullable();
            $table->date('date');
            $table->enum('status', ['reported', 'resolved'])->default('reported');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('catalog_products');
        Schema::dropIfExists('bls');
    }
};