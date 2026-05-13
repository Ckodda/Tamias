<?php

namespace App\Actions\PendingExpense;

use App\Http\Requests\PendingExpense\CreatePendingExpenseRequest;
use App\Http\Requests\PendingExpense\UpdatePendingExpenseRequest;
use App\Http\Responses\PendingExpense\PendingExpenseResponse;
use App\Repositories\Contracts\PendingExpenseRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdatePendingExpenseAction
{
    public function __construct(
        protected PendingExpenseRepositoryInterface $repository,
    )
    { }

    public function execute(UpdatePendingExpenseRequest $request): PendingExpenseResponse
    {
        $paymentMethod = $this->repository->update(
            id: $request->Id,
            costCenterId: $request->CostCenterId,
            expenseDescription: $request->ExpenseDescription,
            totalAmount: $request->TotalAmount,
            dueDate: $request->DueDate,
            providerName: $request->ProviderName,
            paymentStatus: $request->PaymentStatus,
            isActive: $request->IsActive,
            updatedBy: Auth::id()
        );

        return PendingExpenseResponse::fromModel($paymentMethod);
    }
}
