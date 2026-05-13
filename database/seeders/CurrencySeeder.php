<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'CurrencyCode'   => 'PEN',
                'CurrencyName'   => 'Soles',
                'CurrencySymbol' => 'S/',
                'ExchangeRate'   => 1.0000,
                'IsActive'       => true,
                'CreatedAt'      => now(),
                'UpdatedAt'      => now(),
            ],
            [
                'CurrencyCode'   => 'USD',
                'CurrencyName'   => 'Dólares Estadounidenses',
                'CurrencySymbol' => '$',
                'ExchangeRate'   => 3.7500, // Tipo de cambio referencial
                'IsActive'       => true,
                'CreatedAt'      => now(),
                'UpdatedAt'      => now(),
            ],
            [
                'CurrencyCode'   => 'EUR',
                'CurrencyName'   => 'Euros',
                'CurrencySymbol' => '€',
                'ExchangeRate'   => 4.1000,
                'IsActive'       => true,
                'CreatedAt'      => now(),
                'UpdatedAt'      => now(),
            ],
        ];

        foreach ($currencies as $currency) {
            // Usamos updateOrInsert para evitar duplicados si corres el seeder varias veces
            DB::table('Currencies')->updateOrInsert(
                ['CurrencyCode' => $currency['CurrencyCode']],
                $currency
            );
        }
    }
}
