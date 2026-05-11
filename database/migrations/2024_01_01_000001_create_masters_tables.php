<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CostCenters', function (Blueprint $table) {
            $table->id('Id');
            $table->string('CenterName');
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });

        Schema::create('PaymentMethods', function (Blueprint $table) {
            $table->id('Id');
            $table->string('MethodName');
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });

        Schema::create('Currencies', function (Blueprint $table) {
            $table->id('Id');
            $table->char('CurrencyCode', 3);
            $table->string('CurrencySymbol', 5);
            $table->decimal('ExchangeRate', 10, 4);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Currencies');
        Schema::dropIfExists('PaymentMethods');
        Schema::dropIfExists('CostCenters');
    }
};
