<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add additional fields to users table for EIMS
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->after('id');
            $table->string('lastname')->after('firstname');
            $table->string('middlename')->nullable()->after('lastname');
            $table->foreignId('department_id')->nullable()->after('email')->constrained()->onDelete('set null');
            $table->string('employee_id')->nullable()->unique()->after('department_id');
            $table->string('contact_number')->nullable()->after('employee_id');
            $table->string('avatar')->nullable()->after('contact_number');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });

        // Remove the old 'name' column and create a virtual one or accessor in model
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'firstname',
                'lastname',
                'middlename',
                'department_id',
                'employee_id',
                'contact_number',
                'avatar',
                'is_active',
                'last_login_at',
                'deleted_at'
            ]);
        });
    }
};
