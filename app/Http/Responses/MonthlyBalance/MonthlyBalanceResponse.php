<?php

namespace App\Http\Responses\MonthlyBalance;

use App\Models\MonthlyBalance;
use Spatie\LaravelData\Data;

class MonthlyBalanceResponse extends Data
{
    public function __construct(
        public int $Id,
        public string $MonthPeriod,
        public float $TotalIncomes,
        public float $TotalExpenses,
        public float $ClosingBalance,
        public int $CostCenterId,
        // Campos adicionales del Procedure
        public ?string $CenterName,
        public ?float $ProfitMarginPercentage
    ) {}

    /**
     * Crea una instancia de respuesta a partir del modelo hidratado.
     * Eloquent permite acceder a CenterName
     * fueron incluidos en el SELECT del Procedure.
     */
    public static function fromModel(MonthlyBalance $model): self
    {
        return new self(
            Id: $model->Id,
            MonthPeriod: $model->MonthPeriod->format('Y-m'),
            TotalIncomes: (float) $model->TotalIncomes,
            TotalExpenses: (float) $model->TotalExpenses,
            ClosingBalance: (float) $model->ClosingBalance,
            CostCenterId: $model->CostCenterId,
            // Acceso a atributos dinámicos del cursor
            CenterName: $model->CenterName,
            ProfitMarginPercentage: isset($model->ProfitMarginPercentage)
                ? round((float) $model->ProfitMarginPercentage, 2)
                : null
        );
    }
}
