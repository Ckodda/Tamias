<?php

namespace App\Actions\Transaction;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class VoidTransactionAction
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
    )
    { }

    public function execute(int $id): bool
    {
        // Solo llamamos al repositorio pasando el ID y el usuario autenticado
        return $this->repository->void(
            id: $id,
            updatedBy: Auth::id()
        );
    }
}
