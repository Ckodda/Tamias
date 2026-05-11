<?php

namespace App\Http\Responses\Auth;

use Spatie\LaravelData\Data;

class LoginResponse extends Data
{
    public function __construct(
        public string $AccessToken,
        public string $TokenType,
        public int $ExpiresIn,
        public array $User,
    ) {}
}
