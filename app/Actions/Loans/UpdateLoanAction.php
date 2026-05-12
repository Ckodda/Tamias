<?php

namespace App\Actions\Loans;

use App\Http\Requests\Loan\UpdateLoanRequest;
use App\Http\Responses\Loan\LoanResponse;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateLoanAction
{
    public function __construct(
        protected LoanRepositoryInterface $repository
    )
    { }

    /**
     * @throws \Exception
     */
    public function execute(UpdateLoanRequest $request): LoanResponse
    {
        try {
            $userId = Auth::id();

            $event = $this->repository->update(
                id: $request->Id,
                lenderName: $request->LenderName,
                principalAmount: $request->PrincipalAmount,
                interestAmount: $request->InterestAmount,
                totalToRepay: $request->TotalToRepay,
                repaymentDueDate: $request->RepaymentDueDate,
                loanStatus: $request->LoanStatus,
                isActive: true,
                currencyId: $request->CurrencyId,
                updatedBy: $userId
            );

            return LoanResponse::fromModel($event);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
