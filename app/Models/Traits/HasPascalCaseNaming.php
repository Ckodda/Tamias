<?php

namespace App\Models\Traits;

trait HasPascalCaseNaming
{
    /**
     * Inicializar el trait para asegurar la configuración PascalCase.
     */
    public function initializeHasPascalCaseNaming(): void
    {
        $this->primaryKey = 'Id';
        $this->incrementing = true;
    }

    /**
     * Sobrescribimos los métodos de Eloquent para devolver los nombres PascalCase de las columnas.
     * Esto evita conflictos de constantes con la clase Model base.
     */
    public function getCreatedAtColumn(): string
    {
        return 'CreatedAt';
    }

    public function getUpdatedAtColumn(): string
    {
        return 'UpdatedAt';
    }
}
