<?php

namespace App\Actions\Currency;

use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Http\Responses\Currency\CurrencyResponse;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateCurrencyAction
{
    public function __construct(
        protected CurrencyRepositoryInterface $repository
    ) {}

    /**
     * @throws \Exception
     */
    public function execute(UpdateCurrencyRequest $request): CurrencyResponse
    {
        try {
            $userId = Auth::id();

            $currency = $this->repository->update(
                id: $request->Id,
                currencyName: $request->CurrencyName,
                currencyCode: $request->CurrencyCode,
                currencySymbol: $request->CurrencySymbol,
                exchangeRate: $request->ExchangeRate,
                isActive: $request->IsActive,
                updatedBy: $userId
            );

            return CurrencyResponse::fromModel($currency);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
