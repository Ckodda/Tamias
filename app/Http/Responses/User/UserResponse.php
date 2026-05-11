<?php

namespace App\Http\Responses\User;

use App\Models\User;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;

class UserResponse extends Data
{
    public function __construct(
        public int $Id,
        public string $FullName,
        public string $Email,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    public static function fromModel(User $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            FullName: $model->FullName,
            Email: $model->Email,
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
