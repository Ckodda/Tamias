<?php

namespace App\Actions\PaymentMethod;

use App\Http\Requests\PaymentMethod\CreatePaymentMethodRequest;
use App\Http\Responses\PaymentMethod\PaymentMethodResponse;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreatePaymentMethodAction
{
    public function __construct(
        protected PaymentMethodRepositoryInterface $repository
    ) {}

    public function execute(CreatePaymentMethodRequest $request): PaymentMethodResponse
    {
        $paymentMethod = $this->repository->create(
            methodName: $request->MethodName,
            createdBy: Auth::id()
        );

        return PaymentMethodResponse::fromModel($paymentMethod);
    }
}
