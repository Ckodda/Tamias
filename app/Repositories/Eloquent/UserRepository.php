<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserCapabilities;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @throws ValidationException|Exception
     */
    public function create(string $fullName, string $email, string $password, int $createdBy): User
    {
        $cursorName = 'rs_User';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"UsersPkg"."CreateUser"',
                parameters: [$fullName, $email, $password,$createdBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) { throw new Exception("Sin respuesta de la base de datos."); }

            $row = $results[0];
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['Email' => [$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw new Exception($row->ErrorMessage); }

            return $this->mapResultToModel($row, User::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }

    /**
     * @throws Exception
     */
    public function getAll(?int $id = null, ?string $fullName = null, ?string $email = null, ?bool $isActive = null, int $pageSize = 10, int $pageNumber = 1): Collection
    {
        $cursorName = 'rs_Users';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"UsersPkg"."GetUsers"',
                parameters: [$id, $fullName, $email, $isActive, $pageSize, $pageNumber],
                cursorName: $cursorName
            );
            DB::commit();

            // Check for errors from the stored procedure
            if (!empty($results) && isset($results[0]->ErrorId) && $results[0]->ErrorId > 0) {
                throw new Exception($results[0]->ErrorMessage);
            }

            return $this->mapResultsToCollection($results, User::class);
        } catch (Exception $e) { DB::rollBack(); throw $e; }
    }

    /**
     * @throws ValidationException|Exception
     */
    public function update(
        int     $id,
        ?string $fullName = null,
        ?string $email = null,
        ?string $password = null,
        ?bool   $isActive = null,
        ?int    $updatedBy = null
    ): User
    {
        $cursorName = 'rs_User';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"UsersPkg"."UpdateUser"',
                parameters: [$id, $fullName, $email, $password, $isActive, $updatedBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) { throw new Exception("Sin respuesta de la base de datos."); }
            $row = $results[0];
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['Id' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 2) { throw ValidationException::withMessages(['Email' => [$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw new Exception($row->ErrorMessage); }

            return $this->mapResultToModel($row, User::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }

    /**
     * Obtiene los roles y permisos efectivos mapeados al objeto UserRolesAndPermissions.
     * @throws Exception
     */
    public function getRolesAndPermissions(int $userId): \Illuminate\Support\Collection
    {
        $cursorName = 'rs_RolesPermissions';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"UsersPkg"."GetUserRolesAndPermissions"',
                parameters: [$userId],
                cursorName: $cursorName
            );
            DB::commit();

            // Validación de errores de lógica de negocio devueltos por el SP
            if (!empty($results) && isset($results[0]->ErrorId) && $results[0]->ErrorId > 0) {
                throw new Exception($results[0]->ErrorMessage);
            }

            // Mapeamos a la clase Virtual para eliminar la incertidumbre de tipos
            return $this->mapResultsToObjects($results, UserCapabilities::class);
        } catch (Exception $e) { DB::rollBack(); throw $e; }
    }
}
