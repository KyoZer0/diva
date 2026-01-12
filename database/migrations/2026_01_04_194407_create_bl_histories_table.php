<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bl_id')->constrained('bls')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users'); // Who made the change
            $table->string('action'); // e.g., 'status_change', 'note', 'update'
            $table->text('details')->nullable(); // "Changed status from Loading to Delivered"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_histories');
    }
};