<?php

namespace Database\Seeders;

use App\Http\Responses\PaymentMethod\PaymentMethodResponse;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CurrencySeeder::class,
            CostCenterSeeder::class,
            EventSeeder::class,
            PaymentMethodSeeder::class,
            PendingExpenseSeeder::class,
            LoanSeeder::class,
            CommitmentSeeder::class,
            TransactionSeeder::class
        ]);
    }
}
