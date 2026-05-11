<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PendingExpenses', function (Blueprint $table) {
            $table->id('Id');
            $table->foreignId('CostCenterId')->constrained('CostCenters', 'Id');
            $table->text('ExpenseDescription');
            $table->decimal('TotalAmount', 15, 2);
            $table->date('DueDate');
            $table->string('ProviderName');
            $table->enum('PaymentStatus', ['Pending', 'Paid', 'Cancelled']);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });

        Schema::create('Loans', function (Blueprint $table) {
            $table->id('Id');
            $table->foreignId('EventId')->nullable()->constrained('Events', 'Id');
            $table->string('LenderName');
            $table->decimal('PrincipalAmount', 15, 2);
            $table->decimal('InterestAmount', 15, 2);
            $table->decimal('TotalToRepay', 15, 2);
            $table->date('RepaymentDueDate');
            $table->enum('LoanStatus', ['Pending', 'Paid']);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Loans');
        Schema::dropIfExists('PendingExpenses');
    }
};
