<?php

namespace App\Actions\Loans;

use App\Http\Requests\Loan\CreateLoanRequest;
use App\Http\Responses\Loan\LoanResponse;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateLoanAction
{
    public function __construct(
        protected LoanRepositoryInterface $repository,
    )
    { }

    /**
     * @throws \Exception
     */
    public function execute(CreateLoanRequest $request): LoanResponse
    {
        try {
            $userId = Auth::id();

            $loan = $this->repository->create(
                lenderName: $request->LenderName,
                principalAmount: $request->PrincipalAmount,
                interestAmount: $request->InterestAmount,
                totalToRepay: $request->TotalToRepay,
                repaymentDueDate: $request->RepaymentDueDate,
                loanStatus: $request->LoanStatus,
                isActive: true,
                currencyId: $request->CurrencyId,
                createdBy: $userId,
                updatedBy: $userId
            );

            return LoanResponse::fromModel($loan);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
