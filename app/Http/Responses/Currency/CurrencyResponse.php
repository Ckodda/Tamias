<?php

namespace App\Http\Responses\Currency;

use App\Models\Currency;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;

class CurrencyResponse extends Data
{
    public function __construct(
        public int $Id,
        public string $CurrencyName,
        public string $CurrencySymbol,
        public float $ExchangeRate,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    public static function fromModel(Currency $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            CurrencyName: $model->CurrencyName,
            CurrencySymbol: $model->CurrencySymbol,
            ExchangeRate: $model->ExchangeRate,
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
