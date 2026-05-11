<?php

namespace App\Http\Requests\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;

class LoginRequest extends Data
{
    public function __construct(
        #[Required, Email]
        public string $Email,

        #[Required]
        public string $Password,
    ) {}
}
