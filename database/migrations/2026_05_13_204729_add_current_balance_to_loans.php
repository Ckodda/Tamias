<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('Loans', function (Blueprint $blueprint) {
            // Agregamos la columna CurrentBalance
            // Se usa decimal/numeric para precisión financiera
            $blueprint->decimal('CurrentBalance', 15, 2)->default(0)->after('TotalToRepay');
        });

        // IMPORTANTE: Inicializar el CurrentBalance con el monto original del préstamo
        // para los registros que ya existen en la base de datos.
        DB::table('Loans')->update([
            'CurrentBalance' => DB::raw('"TotalToRepay"')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Loans', function (Blueprint $blueprint) {
            $blueprint->dropColumn('CurrentBalance');
        });
    }
};
