<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use stdClass;

abstract class BaseRepository
{
    /**
     * Ejecuta un procedimiento almacenado en PostgreSQL que devuelve un cursor.
     *
     * @param string $procedure Nombre del procedimiento (ej: "Pkg"."Proc")
     * @param array $parameters Parámetros de entrada
     * @param string $cursorName Nombre del cursor de salida
     * @return array Resultados del FETCH
     */
    protected function callProcedure(string $procedure, array $parameters, string $cursorName = 'rs_result'): array
    {
        $allParams = array_merge($parameters, [$cursorName]);
        $placeholders = implode(', ', array_fill(0, count($allParams), '?'));

        DB::statement("CALL $procedure($placeholders)", $allParams);

        return DB::select("FETCH ALL IN \"$cursorName\"");
    }

    /**
     * Mapea un objeto stdClass a una instancia de modelo Eloquent.
     *
     * @template T of Model
     * @param stdClass $result El objeto stdClass a mapear.
     * @param class-string<T> $modelClass La clase del modelo Eloquent.
     * @return T La instancia del modelo hidratada.
     */
    protected function mapResultToModel(stdClass $result, string $modelClass): Model
    {
        /** @var T $instance */
        $instance = new $modelClass();

        return $instance->newFromBuilder((array) $result);
    }

    /**
     * Mapea un array de objetos stdClass a una colección de modelos Eloquent.
     *
     * @template T of Model
     * @param array $results Array de objetos stdClass.
     * @param class-string<T> $modelClass La clase del modelo Eloquent.
     * @return Collection<int, T> Colección de modelos hidratados.
     */
    protected function mapResultsToCollection(array $results, string $modelClass): Collection
    {
        /** @var T $instance */
        $instance = new $modelClass();

        $models = array_map(function ($result) use ($modelClass) {
            return $this->mapResultToModel($result, $modelClass);
        }, $results);

        return $instance->newCollection($models);
    }
}
