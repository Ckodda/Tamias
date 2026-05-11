<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS "PaymentMethodsPkg"');

        $scripts = [
            'CreatePaymentMethod.sql',
            'GetPaymentMethods.sql',
            'UpdatePaymentMethod.sql'
        ];

        foreach ($scripts as $script) {
            $path = database_path("scripts/PaymentMethodsPkg/$script");
            if (File::exists($path)) {
                DB::unprepared(File::get($path));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS "PaymentMethodsPkg"."CreatePaymentMethod"(VARCHAR, BIGINT, REFCURSOR)');
        DB::statement('DROP PROCEDURE IF EXISTS "PaymentMethodsPkg"."GetPaymentMethods"(BIGINT, VARCHAR, BOOLEAN, INTEGER, INTEGER, REFCURSOR)');
        DB::statement('DROP PROCEDURE IF EXISTS "PaymentMethodsPkg"."UpdatePaymentMethod"(BIGINT, VARCHAR, BOOLEAN, BIGINT, REFCURSOR)');
    }
};
