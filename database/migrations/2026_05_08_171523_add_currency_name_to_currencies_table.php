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
        Schema::table('Currencies', function (Blueprint $table) {
            $table->string('CurrencyName', 50)->after('Id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Currencies', function (Blueprint $table) {
            $table->dropColumn('CurrencyName');
        });
    }
};
