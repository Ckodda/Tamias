<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    /**
     * Boot del trait para registrar los eventos de Eloquent.
     */
    public static function bootHasAuditColumns(): void
    {
        // Al crear un nuevo registro
        static::creating(function ($model) {
            if (Auth::check()) {
                $userId = Auth::id();
                if (!$model->isDirty('CreatedBy')) {
                    $model->CreatedBy = $userId;
                }
                if (!$model->isDirty('UpdatedBy')) {
                    $model->UpdatedBy = $userId;
                }
            }
        });

        // Al actualizar un registro existente
        static::updating(function ($model) {
            if (Auth::check()) {
                $userId = Auth::id();
                if (!$model->isDirty('UpdatedBy')) {
                    $model->UpdatedBy = $userId;
                }
            }
        });
    }
}
