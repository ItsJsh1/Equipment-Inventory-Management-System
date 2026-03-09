<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table handles equipment maintenance records
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('maintenance_code')->unique();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('restrict');
            
            $table->enum('type', [
                'preventive',
                'corrective',
                'emergency',
                'inspection'
            ]);
            
            $table->string('title');
            $table->text('description');
            $table->text('issues_found')->nullable();
            $table->text('actions_taken')->nullable();
            $table->text('parts_replaced')->nullable();
            
            $table->date('scheduled_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('technician_name')->nullable();
            $table->string('vendor_name')->nullable();
            
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('scheduled');
            
            $table->enum('equipment_condition_before', ['good', 'fair', 'poor', 'damaged'])->nullable();
            $table->enum('equipment_condition_after', ['good', 'fair', 'poor', 'damaged'])->nullable();
            
            $table->text('remarks')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
