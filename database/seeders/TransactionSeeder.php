<?php

namespace Database\Seeders;

use App\Models\CostCenter;
use App\Models\Currency;
use App\Models\Event;
use App\Models\Loan;
use App\Models\PaymentMethod;
use App\Models\PendingExpense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Recuperamos IDs base para las relaciones
        $adminUser = User::where('Email', 'admin@example.com')->first() ?? User::find(1);
        $costCenter = CostCenter::first();
        $currency = Currency::where('CurrencyCode', 'PEN')->first() ?? Currency::first();
        $paymentMethod = PaymentMethod::where('MethodName', 'Efectivo')->first() ?? PaymentMethod::first();
        $event = Event::first();

        // 2. Obtenemos datos para simular los efectos secundarios
        $pendingExpense = PendingExpense::where('PaymentStatus', 'Pending')->first();
        $loan = Loan::where('CurrentBalance', '>', 0)->first();

        $transactions = [
            // Caso 1: Ingreso por un evento (Aporte ordinario)
            [
                'UserId'                 => $adminUser->Id,
                'CostCenterId'           => $costCenter->Id,
                'EventId'                => $event?->Id,
                'PendingExpenseId'       => null,
                'LoanId'                 => null,
                'CurrencyId'             => $currency->Id,
                'PaymentMethodId'        => $paymentMethod->Id,
                'TransactionAmount'      => 150.00,
                'TransactionType'        => 'Income',
                'AppliedExchangeRate'    => 1.00,
                'AccountingPeriod'       => now()->format('Y-m-d'),
                'TransactionDescription' => 'Cobro de cuota mensual de mantenimiento',
                'ReceiptImagePath'       => null,
                'CreatedBy'              => $adminUser->Id,
            ],
            // Caso 2: Egreso para pagar un gasto pendiente (Luz, Agua, etc.)
            [
                'UserId'                 => $adminUser->Id,
                'CostCenterId'           => $costCenter->Id,
                'EventId'                => null,
                'PendingExpenseId'       => $pendingExpense?->Id,
                'LoanId'                 => null,
                'CurrencyId'             => $currency->Id,
                'PaymentMethodId'        => $paymentMethod->Id,
                'TransactionAmount'      => $pendingExpense ? $pendingExpense->TotalAmount : 50.00,
                'TransactionType'        => 'Expense',
                'AppliedExchangeRate'    => 1.00,
                'AccountingPeriod'       => now()->format('Y-m-d'),
                'TransactionDescription' => 'Pago de servicio registrado como pendiente',
                'ReceiptImagePath'       => 'receipts/factura_servicio_001.jpg',
                'CreatedBy'              => $adminUser->Id,
            ],
            // Caso 3: Egreso que amortiza un préstamo (Pago de deuda)
            [
                'UserId'                 => $adminUser->Id,
                'CostCenterId'           => $costCenter->Id,
                'EventId'                => null,
                'PendingExpenseId'       => null,
                'LoanId'                 => $loan?->Id,
                'CurrencyId'             => $currency->Id,
                'PaymentMethodId'        => $paymentMethod->Id,
                'TransactionAmount'      => 200.00,
                'TransactionType'        => 'Expense',
                'AppliedExchangeRate'    => 1.00,
                'AccountingPeriod'       => now()->format('Y-m-d'),
                'TransactionDescription' => 'Amortización parcial de préstamo',
                'ReceiptImagePath'       => null,
                'CreatedBy'              => $adminUser->Id,
            ],
        ];

        foreach ($transactions as $data) {
            // Insertar la transacción
            DB::table('Transactions')->insert(array_merge($data, [
                'UpdatedBy' => $data['CreatedBy'],
                'CreatedAt' => now(),
                'UpdatedAt' => now(),
            ]));

            // --- SIMULACIÓN DE LÓGICA DEL PROCEDURE ---

            // Si hay gasto pendiente, se marca como pagado
            if ($data['PendingExpenseId']) {
                DB::table('PendingExpenses')
                    ->where('Id', $data['PendingExpenseId'])
                    ->update(['PaymentStatus' => 'Paid', 'UpdatedAt' => now()]);
            }

            // Si hay préstamo, se descuenta del CurrentBalance (imagen_2.png)
            if ($data['LoanId']) {
                $amount = $data['TransactionAmount'];

                // Actualizar saldo
                DB::table('Loans')
                    ->where('Id', $data['LoanId'])
                    ->update([
                        'CurrentBalance' => DB::raw("\"CurrentBalance\" - $amount"),
                        'UpdatedAt' => now()
                    ]);

                // Verificar si se completó el pago para actualizar LoanStatus
                $updatedLoan = DB::table('Loans')->where('Id', $data['LoanId'])->first();
                if ($updatedLoan && $updatedLoan->CurrentBalance <= 0) {
                    DB::table('Loans')
                        ->where('Id', $data['LoanId'])
                        ->update(['LoanStatus' => 'Paid']);
                }
            }
        }
    }
}
