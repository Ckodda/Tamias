<?php

namespace App\Actions\Loan;

use App\Http\Requests\Loan\GetLoansRequest;
use App\Http\Responses\Loan\LoanResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\LoanRepositoryInterface;

class GetLoansAction
{
    public function __construct(
        protected LoanRepositoryInterface $repository
    )
    { }
    /**
     * Ejecuta la lógica para obtener la lista de prestamos paginada.
     *
     * @param GetLoansRequest $request
     * @return PaginatedResponse
     */
    public function execute(GetLoansRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            lenderName: $request->LenderName,
            currencyId: $request->CurrencyId,
            repaymentDueDate: $request->RepaymentDueDate,
            isActive: $request->IsActive,
            loanStatus: $request->LoanStatus,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos cada item al objeto Response con lógica de visibilidad por roles
        $items = $collection->map(fn($event) => LoanResponse::fromModel($event));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
