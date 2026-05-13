<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthlyBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('Email', 'admin@example.com')->first() ?? User::find(1);

        // Corregimos la consulta para que el GROUP BY use la expresión completa, no el alias
        $balances = DB::table('Transactions')
            ->select(
                'CostCenterId',
                DB::raw("DATE_TRUNC('month', \"AccountingPeriod\")::date as \"MonthPeriod\""),
                DB::raw("SUM(CASE WHEN \"TransactionType\" = 'Income' THEN \"TransactionAmount\" ELSE 0 END) as \"TotalIncomes\""),
                DB::raw("SUM(CASE WHEN \"TransactionType\" = 'Expense' THEN \"TransactionAmount\" ELSE 0 END) as \"TotalExpenses\"")
            )
            ->groupBy('CostCenterId', DB::raw("DATE_TRUNC('month', \"AccountingPeriod\")::date"))
            ->get();

        foreach ($balances as $data) {
            $closingBalance = $data->TotalIncomes - $data->TotalExpenses;

            DB::table('MonthlyBalances')->updateOrInsert(
                [
                    'CostCenterId' => $data->CostCenterId,
                    'MonthPeriod'  => $data->MonthPeriod,
                ],
                [
                    'TotalIncomes'   => $data->TotalIncomes,
                    'TotalExpenses'  => $data->TotalExpenses,
                    'ClosingBalance' => $closingBalance,
                    'CreatedBy'      => $adminUser->Id,
                    'UpdatedBy'      => $adminUser->Id,
                    'CreatedAt'      => now(),
                    'UpdatedAt'      => now(),
                    'IsActive'       => true
                ]
            );
        }
    }
}
