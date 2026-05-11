<?php

namespace App\Http\Requests\User;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateUserRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int    $Id,
        #[Nullable, StringType, Max(255)]
        public ?string $FullName,
        #[Nullable, Email, Max(255)]
        public ?string $Email,
        #[Nullable, StringType, Min(8), Max(255)]
        public ?string $Password,
        #[Nullable, BooleanType]
        public ?bool   $IsActive
    ) {}
}
