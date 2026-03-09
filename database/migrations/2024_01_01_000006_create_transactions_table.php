<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table handles incoming and outgoing equipment transactions
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('restrict');
            $table->enum('type', [
                'incoming',
                'outgoing',
                'borrow',
                'return',
                'transfer'
            ]);
            
            // Person involved in transaction
            $table->string('person_firstname');
            $table->string('person_lastname');
            $table->string('person_middlename')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            
            // Transaction details
            $table->date('transaction_date');
            $table->date('expected_return_date')->nullable(); // For borrowed items
            $table->date('actual_return_date')->nullable();   // When item is returned
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            
            $table->enum('status', [
                'pending',
                'approved',
                'completed',
                'cancelled',
                'overdue'
            ])->default('pending');
            
            // Approval workflow
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            
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
        Schema::dropIfExists('transactions');
    }
};
