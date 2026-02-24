<?php

namespace App\DTOs;

use App\Enums\UserType;

readonly class StaffDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public ?string $password,
        public string $user_type,
        public array $roles = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? '',
            password: $data['password'] ?? null,
            user_type: $data['user_type'] ?? UserType::STAFF->value,
            roles: $data['roles'] ?? []
        );
    }
}
