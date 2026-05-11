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
        Schema::table('Events', function (Blueprint $table) {
            $table->foreignId('CurrencyId')->nullable()->after('CostCenterId')->constrained('Currencies', 'Id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('CurrencyId');
        });
    }
};
