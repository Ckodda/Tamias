<?php

namespace App\Actions\Currency;

use App\Http\Requests\Currency\CreateCurrencyRequest;
use App\Http\Responses\Currency\CurrencyResponse;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateCurrencyAction
{
    public function __construct(
        protected CurrencyRepositoryInterface $repository
    ) {}

    /**
     * @throws \Exception
     */
    public function execute(CreateCurrencyRequest $request): CurrencyResponse
    {
        try {
            $userId = Auth::id();

            $currency = $this->repository->create(
                currencyName: $request->CurrencyName,
                currencyCode: $request->CurrencyCode,
                currencySymbol: $request->CurrencySymbol,
                exchangeRate: $request->ExchangeRate,
                createdBy: $userId
            );

            return CurrencyResponse::fromModel($currency);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
