<?php

namespace App\Http\Requests\User;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateUserRequest extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $FullName,
        #[Required, Email, Max(255)]
        public string $Email,
        #[Required, StringType, Min(8), Max(255)]
        public string $Password
    ) {}
}
