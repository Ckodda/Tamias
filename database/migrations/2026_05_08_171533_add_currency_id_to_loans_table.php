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
        Schema::table('Loans', function (Blueprint $table) {
            $table->unsignedBigInteger('CurrencyId')->nullable()->after('Id'); // Assuming 'id' is the first column
            $table->foreign('CurrencyId')->references('Id')->on('Currencies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Loans', function (Blueprint $table) {
            $table->dropForeign(['CurrencyId']);
            $table->dropColumn('CurrencyId');
        });
    }
};
