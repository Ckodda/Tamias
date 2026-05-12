<?php

namespace App\Actions\Transaction;

use App\Http\Requests\Transaction\CreateTransactionRequest;
use App\Http\Responses\Transaction\TransactionResponse;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CreateTransactionAction
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
    )
    { }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function execute(CreateTransactionRequest $request): TransactionResponse
    {
        try {
            $userId = Auth::id();
            $receiptPath = null;

            // 1. Procesar la subida de la imagen si existe
            if ($request->ReceiptImage) {
                // Guardamos en el disco 'public' dentro de la carpeta 'receipts'
                // Esto genera un nombre único automáticamente
                $receiptPath = $request->ReceiptImage->store('receipts', 'public');
            }

            // 2. Llamada al repositorio con el orden exacto de parámetros del Procedure
            $transaction = $this->repository->create(
                userId: $request->UserId,
                costCenterId: $request->CostCenterId,
                currencyId: $request->CurrencyId,
                paymentMethodId: $request->PaymentMethodId,
                transactionAmount: $request->TransactionAmount,
                transactionType: $request->TransactionType,
                accountingPeriod: $request->AccountingPeriod,
                transactionDescription: $request->TransactionDescription,
                createdBy: $userId,
                eventId: $request->EventId,
                pendingExpenseId: $request->PendingExpenseId,
                loanId: $request->LoanId,
                appliedExchangeRate: $request->AppliedExchangeRate,
                receiptImagePath: $receiptPath // Pasamos el string de la ruta, no el objeto File
            );

            return TransactionResponse::fromModel($transaction);

        } catch (ValidationException $e) {
            // Si el repositorio lanza error pero se subió una imagen, podríamos limpiarla (opcional)
            if (isset($receiptPath)) {
                Storage::disk('public')->delete($receiptPath);
            }
            throw $e;
        } catch (Exception $e) {
            if (isset($receiptPath)) {
                Storage::disk('public')->delete($receiptPath);
            }
            throw new Exception("Error al procesar la transacción: " . $e->getMessage());
        }
    }
}
