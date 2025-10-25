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
            $table->enum('client_type', ['individual', 'company'])->default('individual')->after('name');
            $table->string('company_name')->nullable()->after('client_type');
            $table->string('contact_person')->nullable()->after('company_name');
            $table->string('phone')->nullable()->after('contact');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('country')->default('Morocco')->after('postal_code');
            $table->text('notes')->nullable()->after('likes');
            $table->enum('status', ['lead', 'prospect', 'customer', 'inactive'])->default('lead')->after('notes');
            $table->decimal('budget_range', 10, 2)->nullable()->after('status');
            $table->date('last_contact_date')->nullable()->after('budget_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'client_type', 'company_name', 'contact_person', 'phone', 'email',
                'address', 'postal_code', 'country', 'notes', 'status',
                'budget_range', 'last_contact_date'
            ]);
        });
    }
};
