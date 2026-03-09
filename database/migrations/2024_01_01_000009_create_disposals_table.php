<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table handles equipment disposal records
     */
    public function up(): void
    {
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();
            $table->string('disposal_code')->unique();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('restrict');
            
            $table->enum('method', [
                'sale',
                'donation',
                'recycling',
                'destruction',
                'trade_in',
                'other'
            ]);
            
            $table->text('reason');
            $table->date('disposal_date');
            $table->decimal('disposal_value', 12, 2)->nullable(); // Value received if sold
            $table->string('recipient_name')->nullable(); // Who received the equipment
            $table->string('recipient_contact')->nullable();
            $table->text('documentation')->nullable(); // Reference to documents
            
            $table->enum('status', [
                'pending_approval',
                'approved',
                'completed',
                'cancelled'
            ])->default('pending_approval');
            
            $table->text('remarks')->nullable();
            
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposals');
    }
};
