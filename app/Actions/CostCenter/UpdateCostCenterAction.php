<?php

namespace App\Actions\CostCenter;

use App\Http\Requests\CostCenter\UpdateCostCenterRequest;
use App\Http\Responses\CostCenter\CostCenterResponse;
use App\Repositories\Contracts\CostCenterRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class UpdateCostCenterAction
{
    public function __construct(
        protected CostCenterRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta la lógica para actualizar un centro de costo.
     *
     * @param UpdateCostCenterRequest $request
     * @return CostCenterResponse
     * @throws ValidationException|Exception
     */
    public function execute(UpdateCostCenterRequest $request): CostCenterResponse
    {
        try {
            $userId = Auth::id();

            $costCenter = $this->repository->update(
                id: $request->Id,
                codeCostCenter: $request->CodeCostCenter,
                centerName: $request->CenterName,
                isActive: $request->IsActive,
                updatedBy: $userId
            );

            return CostCenterResponse::fromModel($costCenter);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
