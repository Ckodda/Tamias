<?php

namespace App\Actions\CostCenter;

use App\Http\Requests\CostCenter\CreateCostCenterRequest;
use App\Http\Responses\CostCenter\CostCenterResponse;
use App\Repositories\Contracts\CostCenterRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class CreateCostCenterAction
{
    public function __construct(
        protected CostCenterRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta la lógica para crear un centro de costo.
     *
     * @param CreateCostCenterRequest $request
     * @return CostCenterResponse
     * @throws ValidationException|Exception
     */
    public function execute(CreateCostCenterRequest $request): CostCenterResponse
    {
        try {
            $userId = Auth::id();

            $costCenter = $this->repository->create(
                codeCostCenter: $request->CodeCostCenter,
                centerName: $request->CenterName,
                createdBy: $userId
            );

            return CostCenterResponse::fromModel($costCenter);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
