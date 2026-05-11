<?php

namespace App\Actions\User;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Responses\User\UserResponse;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function execute(UpdateUserRequest $request): UserResponse
    {
        $paymentMethod = $this->repository->update(
            id: $request->Id,
            fullName: $request->FullName,
            email: $request->Email,
            password: $request->Password,
            isActive: $request->IsActive,
            updatedBy: Auth::id()
        );

        return UserResponse::fromModel($paymentMethod);
    }
}
