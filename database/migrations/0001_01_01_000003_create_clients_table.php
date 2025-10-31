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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic info - REQUIRED
            $table->string('full_name'); // Client's full name
            $table->enum('client_type', ['particulier', 'professionnel'])->default('particulier');
            
            // Company info (only for professionnel)
            $table->string('company_name')->nullable();
            
            // Contact info - REQUIRED phone
            $table->string('phone'); // Required field
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            
            // Business details
            $table->string('source')->nullable(); // Where they came from
            $table->json('products')->nullable(); // Products of interest
            $table->string('conseiller')->nullable(); // Sales rep name
            $table->boolean('devis_demande')->default(false); // Quote requested
            
            // Additional notes
            $table->text('notes')->nullable();
            
            // Status tracking
            $table->enum('status', ['visited', 'purchased', 'follow_up'])->default('visited');
            $table->date('last_contact_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};