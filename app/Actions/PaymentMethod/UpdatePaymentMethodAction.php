<?php

namespace App\Actions\PaymentMethod;

use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Responses\PaymentMethod\PaymentMethodResponse;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdatePaymentMethodAction
{
    public function __construct(
        protected PaymentMethodRepositoryInterface $repository
    ) {}

    public function execute(UpdatePaymentMethodRequest $request): PaymentMethodResponse
    {
        $paymentMethod = $this->repository->update(
            id: $request->Id,
            methodName: $request->MethodName,
            isActive: $request->IsActive,
            updatedBy: Auth::id()
        );

        return PaymentMethodResponse::fromModel($paymentMethod);
    }
}
