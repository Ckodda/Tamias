<?php

namespace App\Http\Responses\PaymentMethod;

use App\Models\PaymentMethod;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;

class PaymentMethodResponse extends Data
{
    public function __construct(
        public int $Id,
        public string $MethodName,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    public static function fromModel(PaymentMethod $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            MethodName: $model->MethodName,
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
