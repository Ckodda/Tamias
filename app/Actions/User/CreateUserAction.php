<?php

namespace App\Actions\User;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Responses\User\UserResponse;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $repository
    )
    { }

    public function execute(CreateUserRequest $request): UserResponse
    {
        $user = $this->repository->create(
            fullName: $request->FullName,
            email: $request->Email,
            password: $request->Password,
            createdBy: Auth::id()
        );

        return UserResponse::fromModel($user);
    }

}
