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
            // Drop foreign key first if it exists
//            $table->dropConstrainedForeignId('EventId');
            // Then drop the column
            $table->dropColumn('EventId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Loans', function (Blueprint $table) {
            // Re-add the column and foreign key if rolling back
            $table->foreignId('EventId')->nullable()->constrained('Events', 'Id')->after('Id'); // Assuming 'Id' is the first column
        });
    }
};
