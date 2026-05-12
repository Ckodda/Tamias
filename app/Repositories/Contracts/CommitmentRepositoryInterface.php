<?php

namespace App\Repositories\Contracts;

use App\Models\Commitment;
use Illuminate\Database\Eloquent\Collection;

interface CommitmentRepositoryInterface
{
    public function create(
        int $userId,
        int $costCenterId,
        int $eventId,
        float $commitmentAmount,
        string $frequencyType,
        string $currentStatus,
        int $createdBy
    ): Commitment;
    public function getAll(
        ?int $id = null,
        ?int $userId = null,
        ?int $costCenterId = null,
        ?int $eventId = null,
        ?string $currentStatus = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;

    public function update(
        int $id,
        ?int $userId,
        ?int $costCenterId,
        ?int $eventId,
        ?float $commitmentAmount,
        ?string $frequencyType,
        ?string $currentStatus,
        int $updatedBy
    ): Commitment;
}
