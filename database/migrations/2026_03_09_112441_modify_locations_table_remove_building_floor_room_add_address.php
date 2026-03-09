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
        Schema::table('locations', function (Blueprint $table) {
            // Add the new address field
            $table->text('address')->nullable()->after('name');
            
            // Drop the old fields
            $table->dropColumn(['building', 'floor', 'room', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Re-add the old fields
            $table->string('building')->nullable()->after('name');
            $table->string('floor')->nullable()->after('building');
            $table->string('room')->nullable()->after('floor');
            $table->string('description')->nullable()->after('room');
            
            // Drop the address field
            $table->dropColumn('address');
        });
    }
};
