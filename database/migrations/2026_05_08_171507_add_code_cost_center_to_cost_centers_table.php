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
        Schema::table('CostCenters', function (Blueprint $table) {
            $table->string('CodeCostCenter', 20)->unique()->after('Id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('CostCenters', function (Blueprint $table) {
            $table->dropColumn('CodeCostCenter');
        });
    }
};
