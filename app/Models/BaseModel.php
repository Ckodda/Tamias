<?php

namespace App\Models;

use App\Models\Traits\HasPascalCaseNaming;
use App\Models\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasPascalCaseNaming, HasAuditColumns;
}
