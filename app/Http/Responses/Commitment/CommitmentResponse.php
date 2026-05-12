<?php

namespace App\Http\Responses\Commitment;

use App\Models\Commitment;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Data;

class CommitmentResponse extends Data
{
    public function __construct(
        public int $Id,
        public int $UserId,
        public int $CostCenterId,
        public int $EventId,
        public float $CommitmentAmount,
        public string $FrequencyType,
        public string $CurrentStatus,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt
    )
    { }

    public static function fromModel(Commitment $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            UserId: $model->UserId,
            CostCenterId: $model->CostCenterId,
            EventId: $model->EventId,
            CommitmentAmount: $model->CommitmentAmount,
            FrequencyType: $model->FrequencyType,
            CurrentStatus: $model->CurrentStatus,
            IsActive: $model->IsActive,
            CreatedBy: $model->CreatedBy,
            UpdatedBy: $model->UpdatedBy,
            CreatedAt: $model->CreatedAt->toIso8601String(),
            UpdatedAt: $model->UpdatedAt->toIso8601String()
        );

        // Si no tiene el rol, ocultamos los campos de auditoría del JSON final
        if (!$canSeeAudit) {
            return $data->except('CreatedBy', 'UpdatedBy');
        }

        return $data;
    }
}
