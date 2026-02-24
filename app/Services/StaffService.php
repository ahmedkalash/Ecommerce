<?php

namespace App\Services;

use App\DTOs\StaffDTO;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffService
{
    /**
     * Store a new staff member.
     * The caller must handle the DB transaction.
     */
    public function store(StaffDTO $staffDTO): Admin
    {
        $data = [
            'name' => $staffDTO->name,
            'email' => $staffDTO->email,
            'phone' => $staffDTO->phone,
            'user_type' => $staffDTO->user_type,
        ];
        $data['password'] = Hash::make($staffDTO->password);

        /** @var Admin $staff */
        $staff = Admin::create($data);

        // Assign roles, sync strictly
        if (! empty($staffDTO->roles)) {
            // Find valid roles to avoid Spatie errors with non-existent ones
            $roles = Role::whereIn('id', $staffDTO->roles)->get();
            $staff->syncRoles($roles);
        }

        return $staff;
    }

    /**
     * Update an existing staff member.
     * The caller must handle the DB transaction.
     */
    public function update(StaffDTO $dto, Admin $staff): Admin
    {
        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'user_type' => $dto->user_type,
        ];

        if (! empty($dto->password)) {
            $data['password'] = Hash::make($dto->password);
        }

        $staff->update($data);

        // Assign roles, sync strictly
        $roles = Role::whereIn('id', $dto->roles)->get();
        $staff->syncRoles($roles);

        return $staff;
    }
}
