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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_code')->unique();
            $table->foreignId('brand_id')->constrained()->onDelete('restrict');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->string('model_name');
            $table->string('serial_number')->nullable()->unique();
            $table->text('specifications')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 12, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->enum('status', [
                'available',
                'in_use',
                'borrowed',
                'maintenance',
                'for_disposal',
                'disposed'
            ])->default('available');
            $table->enum('condition', [
                'new',
                'good',
                'fair',
                'poor',
                'damaged'
            ])->default('new');
            $table->text('remarks')->nullable();
            $table->string('image_path')->nullable();
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
        Schema::dropIfExists('equipments');
    }
};
