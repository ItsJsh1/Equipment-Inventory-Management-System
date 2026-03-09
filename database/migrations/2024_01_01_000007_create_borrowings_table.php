<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table handles borrowed equipment tracking
     */
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('borrowing_code')->unique();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('restrict');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            
            // Borrower information
            $table->string('borrower_firstname');
            $table->string('borrower_lastname');
            $table->string('borrower_middlename')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('id_number')->nullable(); // Employee/Student ID
            
            // Borrowing details
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->text('purpose');
            $table->text('remarks')->nullable();
            
            $table->enum('status', [
                'borrowed',
                'returned',
                'overdue',
                'lost',
                'damaged'
            ])->default('borrowed');
            
            // Condition tracking
            $table->enum('condition_on_borrow', ['new', 'good', 'fair', 'poor'])->default('good');
            $table->enum('condition_on_return', ['new', 'good', 'fair', 'poor', 'damaged'])->nullable();
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('borrowings');
    }
};
