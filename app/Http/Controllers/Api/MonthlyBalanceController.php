<?php

namespace App\Http\Controllers\Api;

use App\Actions\MonthlyBalance\GetMonthlyBalancesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MonthlyBalance\GetMonthlyBalancesRequest;
use App\Http\Responses\ApiResponse;
use Exception;

class MonthlyBalanceController extends Controller
{
    /**
     * Listado paginado de MonthlyBalance.
     */
    public function index(GetMonthlyBalancesRequest $request, GetMonthlyBalancesAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);
            return ApiResponse::success(Content: $results, Message: 'Listado obtenido correctamente');
        } catch (Exception $e) {
            return ApiResponse::error(Message: $e->getMessage(), Code: 500);
        }
    }

}
