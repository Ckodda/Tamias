<?php

namespace App\Http\Responses\Event;

use App\Models\Event;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;

class EventResponse extends Data
{
    public function __construct(
        public int $Id,
        public int $CostCenterId,
        public string $EventName,
        public float $TargetAmount,
        public string $EventStatus,
        public string $StartDate,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    public static function fromModel(Event $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            CostCenterId: $model->CostCenterId,
            EventName: $model->EventName,
            TargetAmount: $model->TargetAmount,
            EventStatus: $model->EventStatus,
            StartDate: $model->StartDate->toDateString(),
            IsActive: $model->IsActive,
            CreatedBy: $model->CreatedBy,
            UpdatedBy: $model->UpdatedBy,
            CreatedAt: $model->CreatedAt->toIso8601String(),
            UpdatedAt: $model->UpdatedAt->toIso8601String(),
        );

        if (!$canSeeAudit) {
            return $data->except('CreatedBy', 'UpdatedBy');
        }

        return $data;
    }
}
