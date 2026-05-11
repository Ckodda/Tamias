<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Transactions', function (Blueprint $table) {
            $table->id('Id');
            $table->foreignId('UserId')->nullable()->constrained('Users', 'Id');
            $table->foreignId('CostCenterId')->constrained('CostCenters', 'Id');
            $table->foreignId('EventId')->nullable()->constrained('Events', 'Id');
            $table->foreignId('PendingExpenseId')->nullable()->constrained('PendingExpenses', 'Id');
            $table->foreignId('LoanId')->nullable()->constrained('Loans', 'Id');
            $table->foreignId('CurrencyId')->constrained('Currencies', 'Id');
            $table->foreignId('PaymentMethodId')->constrained('PaymentMethods', 'Id');

            $table->decimal('TransactionAmount', 15, 2);
            $table->enum('TransactionType', ['Income', 'Expense']);
            $table->decimal('AppliedExchangeRate', 10, 4);
            $table->date('AccountingPeriod');
            $table->text('TransactionDescription');
            $table->string('ReceiptImagePath')->nullable();

            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Transactions');
    }
};
