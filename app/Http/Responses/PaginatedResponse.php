<?php

namespace App\Http\Responses;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

/**
 * @template T
 */
class PaginatedResponse extends Data
{
    /**
     * @param Collection<int, T> $Items
     * @param int $TotalCount
     * @param int $PageNumber
     * @param int $PageSize
     * @param int $TotalPages
     */
    public function __construct(
        public Collection $Items,
        public int $TotalCount,
        public int $PageNumber,
        public int $PageSize,
        public int $TotalPages,
    ) {}

    /**
     * Helper para crear una respuesta paginada.
     */
    public static function make(Collection $items, int $totalCount, int $pageNumber, int $pageSize): self
    {
        $totalPages = $pageSize > 0 ? (int) ceil($totalCount / $pageSize) : 0;

        return new self(
            Items: $items,
            TotalCount: $totalCount,
            PageNumber: $pageNumber,
            PageSize: $pageSize,
            TotalPages: $totalPages,
        );
    }
}
