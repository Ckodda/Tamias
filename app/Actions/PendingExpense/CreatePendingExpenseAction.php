<?php

namespace App\Actions\PendingExpense;

use App\Http\Requests\PendingExpense\CreatePendingExpenseRequest;
use App\Http\Responses\PendingExpense\PendingExpenseResponse;
use App\Repositories\Contracts\PendingExpenseRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreatePendingExpenseAction
{
    public function __construct(
        protected PendingExpenseRepositoryInterface $repository,
    )
    { }

    public function execute(CreatePendingExpenseRequest $request): PendingExpenseResponse
    {
        $paymentMethod = $this->repository->create(
            costCenterId: $request->CostCenterId,
            expenseDescription: $request->ExpenseDescription,
            totalAmount: $request->TotalAmount,
            dueDate: $request->DueDate,
            providerName: $request->ProviderName,
            paymentStatus: $request->PaymentStatus,
            createdBy: Auth::id()
        );

        return PendingExpenseResponse::fromModel($paymentMethod);
    }
}
