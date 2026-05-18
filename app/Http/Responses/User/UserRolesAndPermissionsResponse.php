<?php

namespace App\Http\Responses\User;

use App\Models\UserRolesAndPermissions;
use Spatie\LaravelData\Data;

class UserRolesAndPermissionsResponse extends Data
{
    public function __construct(
        public string $Type,
        public string $Name
    ) {}
    
     public static function fromModel(UserRolesAndPermissions $model): self
    {        
          return new self(
               Type: $model->Type,
               Name: $model->Name
          );
        
    }
}