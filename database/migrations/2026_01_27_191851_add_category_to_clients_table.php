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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('professional_category')->nullable()->after('company_name'); // revendeur, architecte, promoteur
            $table->integer('potential_score')->default(50)->after('email');
            $table->string('smart_status')->default('cold')->after('potential_score'); // cold, warm, hot
            $table->timestamp('last_interaction_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['professional_category', 'potential_score', 'smart_status', 'last_interaction_at']);
        });
    }
};
