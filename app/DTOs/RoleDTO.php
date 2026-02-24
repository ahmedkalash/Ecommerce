<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

readonly class RoleDTO
{
    public function __construct(
        public string $name,
        public string $guard_name,
        public Collection $permissions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            guard_name: $data['guard_name'],
            permissions: collect($data['permissions'] ?? []),
        );
    }
}
