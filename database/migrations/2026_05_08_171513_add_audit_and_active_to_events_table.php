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
            $table->boolean('IsActive')->default(true)->after('StartDate');
            $table->foreignId('CreatedBy')->nullable()->constrained('Users', 'Id')->after('UpdatedAt');
            $table->foreignId('UpdatedBy')->nullable()->constrained('Users', 'Id')->after('CreatedBy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Events', function (Blueprint $table) {
            $table->dropColumn('IsActive');
            $table->dropConstrainedForeignId('CreatedBy');
            $table->dropColumn('CreatedBy');
            $table->dropConstrainedForeignId('UpdatedBy');
            $table->dropColumn('UpdatedBy');
        });
    }
};
