<?php

namespace Tests\Feature\Actions\Transaction;

use App\Models\CostCenter;
use App\Models\Currency;
use App\Models\Loan;
use App\Models\PaymentMethod;
use App\Models\PendingExpense;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Crear una transacción de tipo Gasto vinculada a un Préstamo.
     * Verifica que el Stored Procedure actualice el CurrentBalance.
     */
    public function test_its_create_expense_transaction_linked_to_loan(): void
    {
        Storage::fake('public');

        // --- PREPARACIÓN ---
        $user = User::find(1);
        $costCenter = CostCenter::first();
        $currency = Currency::where('CurrencyCode', 'PEN')->first() ?? Currency::first();
        $paymentMethod = PaymentMethod::first();
        $loan = Loan::where('CurrentBalance', '>', 100)->first();

        $initialBalance = $loan->CurrentBalance;
        $paymentAmount = 50.00;

        $data = [
            'UserId' => $user->Id,
            'CostCenterId' => $costCenter->Id,
            'CurrencyId' => $currency->Id,
            'PaymentMethodId' => $paymentMethod->Id,
            'TransactionAmount' => $paymentAmount,
            'TransactionType' => 'Expense',
            'AccountingPeriod' => now()->format('Y-m-d'),
            'TransactionDescription' => 'Pago parcial de préstamo - Test',
            'LoanId' => $loan->Id,
            'ReceiptImage' => UploadedFile::fake()->image('voucher.jpg')
        ];

        // --- EJECUCIÓN ---
        $response = $this->withToken($this->apiToken)
            ->postJson('/api/transactions/', $data);
        // --- ASERCIONES ---
        $response->assertStatus(201)
            ->assertJson([
                'Code' => 201,
                'Message' => 'Transaction creado exitosamente'
            ]);

        // Verificar que el registro existe en la tabla Transactions
        $this->assertDatabaseHas('Transactions', [
            'LoanId' => $loan->Id,
            'TransactionAmount' => $paymentAmount,
            'TransactionType' => 'Expense'
        ]);

        // VERIFICAR EFECTO SECUNDARIO DEL PROCEDURE:
        // El CurrentBalance del Loan debe haber disminuido
        $loan->refresh();
        $this->assertEquals($initialBalance - $paymentAmount, $loan->CurrentBalance);
    }

    /**
     * Test: Anulación de una transacción y reversión de saldos.
     */
    public function test_its_void_transaction_and_reverts_loan_balance(): void
    {
        // 1. Obtener una transacción existente vinculada a un préstamo (del seeder)
        $transaction = Transaction::whereNotNull('LoanId')
            ->where('IsActive', true)
            ->first();

        $loan = Loan::find($transaction->LoanId);
        $balanceBeforeVoid = $loan->CurrentBalance;

        // 2. Ejecutar anulación
        $response = $this->withToken($this->apiToken)
            ->postJson("/api/transactions/{$transaction->Id}/void");
        $response->dump();
        $response->assertStatus(201)
            ->assertJson([
                'Message' => 'Transacción anulada y saldos revertidos exitosamente.'
            ]);

        // 3. Verificar que la transacción se marcó como inactiva
        $this->assertDatabaseHas('Transactions', [
            'Id' => $transaction->Id,
            'IsActive' => false
        ]);

        // 4. Verificar que el saldo del préstamo se revirtió (si era Expense, debe sumar)
        $loan->refresh();
        $this->assertEquals($balanceBeforeVoid + $transaction->TransactionAmount, $loan->CurrentBalance);
    }

    /**
     * Test: Listado paginado con filtros de fecha.
     */
    public function test_its_list_transactions_with_filters(): void
    {
        $queryParams = [
            'StartDate' => now()->startOfMonth()->format('Y-m-d'),
            'EndDate' => now()->endOfMonth()->format('Y-m-d'),
            'PageSize' => 5
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/transactions/?' . http_build_query($queryParams));
        $response->assertStatus(200)
            ->assertJsonStructure([
                'Code',
                'Message',
                'Content' => [
                    'Items' => [
                        '*' => [
                            'Id',
                            'TransactionAmount',
                            'TransactionType',
                            'AccountingPeriod'
                        ]
                    ],
                    'TotalCount'
                ]
            ]);
    }

    /**
     * Test: Error de validación cuando el monto excede el CurrentBalance (Lógica del Procedure).
     */
    public function test_its_fails_when_amount_exceeds_loan_balance(): void
    {
        $loan = Loan::where('CurrentBalance', '>', 0)->first();

        $data = [
            'UserId' => 1,
            'CostCenterId' => CostCenter::first()->Id,
            'CurrencyId' => Currency::first()->Id,
            'PaymentMethodId' => PaymentMethod::first()->Id,
            'TransactionAmount' => $loan->CurrentBalance + 100, // Monto superior a la deuda
            'TransactionType' => 'Expense',
            'AccountingPeriod' => now()->format('Y-m-d'),
            'TransactionDescription' => 'Intento de sobrepago',
            'LoanId' => $loan->Id
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/transactions/', $data);

        // Aquí depende de cómo tu CreateTransactionAction maneje el ErrorId del Procedure
        // Si el Procedure devuelve ErrorId = 9, el Action debería lanzar una excepción capturada como 500 o 422
        $response->assertStatus(500);
        $this->assertStringContainsString('excede el saldo pendiente', $response->json('Message'));
    }
}
