<?php

namespace App\DTOs;

use App\Enums\UserType;
use Spatie\LaravelData\Data;

class UserDTO extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserType $user_type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            user_type: $data['user_type'],
        );
    }
}
