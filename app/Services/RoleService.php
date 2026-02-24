<?php

namespace App\Services;

use App\DTOs\RoleDTO;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Store a new staff member.
     * The caller must handle the DB transaction.
     */
    public function store(RoleDTO $roleDTO): Role
    {
        $role = Role::create([
            'name' => $roleDTO->name,
            'guard_name' => $roleDTO->guard_name,
        ]);

        $permissions = Permission::whereIn('id', $roleDTO->permissions)->get()->pluck('id');
        $role->syncPermissions($permissions);

        return $role;
    }

    /**
     * Update an existing staff member.
     * The caller must handle the DB transaction.
     */
    public function update(RoleDTO $roleDTO, Role $role): Role
    {
        $role->update([
            'name' => $roleDTO->name,
            'guard_name' => $roleDTO->guard_name,
        ]);

        $permissions = Permission::whereIn('id', $roleDTO->permissions)->get()->pluck('id');
        $role->syncPermissions($permissions);

        return $role;
    }
}
