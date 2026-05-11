<?php

namespace App\Http\Responses\CostCenter;

use App\Models\CostCenter;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;

class CostCenterResponse extends Data
{
    public function __construct(
        public int $Id,
        public string $CodeCostCenter,
        public string $CenterName,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    /**
     * Mapea el modelo al objeto de respuesta controlando la visibilidad por roles.
     */
    public static function fromModel(CostCenter $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            CodeCostCenter: $model->CodeCostCenter,
            CenterName: $model->CenterName,
            IsActive: $model->IsActive,
            CreatedBy: $model->CreatedBy,
            UpdatedBy: $model->UpdatedBy,
            CreatedAt: $model->CreatedAt->toIso8601String(),
            UpdatedAt: $model->UpdatedAt->toIso8601String(),
        );

        // Si no tiene el rol, ocultamos los campos de auditoría del JSON final
        if (!$canSeeAudit) {
            return $data->except('CreatedBy', 'UpdatedBy');
        }

        return $data;
    }
}
