<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('MonthlyBalances', function (Blueprint $table) {
            $table->id('Id');
            $table->foreignId('CostCenterId')->constrained('CostCenters', 'Id');
            $table->date('MonthPeriod');
            $table->decimal('TotalIncomes', 15, 2)->default(0);
            $table->decimal('TotalExpenses', 15, 2)->default(0);
            $table->decimal('ClosingBalance', 15, 2)->default(0);

            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();

            $table->unique(['CostCenterId', 'MonthPeriod']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('MonthlyBalances');
    }
};
