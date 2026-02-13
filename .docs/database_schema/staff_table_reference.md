# Staff Roles & Data Structure (Replaced Staff Table)

**Database**: `ecommerce`  
**Status**: The separate `staff` table has been **removed/deprecated**.  
**Current Architecture**: Staff members are now `users` with `user_type='staff'` and assigned Roles/Permissions via
Spatie's `laravel-permission` tables.

---

## Overview

In the updated architecture, "Staff" is no longer a separate entity with its own table. It is a Role assigned to a User.
The system uses a combination of:

1. **Users Table (`users`)**: Stores the core identity (`email`, `password`, `name`) and `user_type = 'staff'`.
2. **Roles & Permissions (`roles`, `permissions`, `model_has_roles`)**: Defines what the staff member can do.

## Data Mapping

| Old Concept        | New Implementation                                               |
|:-------------------|:-----------------------------------------------------------------|
| `staff` table      | **Removed**                                                      |
| `staff.user_id`    | `users.id` (User connects directly to roles)                     |
| `staff.role_id`    | `model_has_roles` table (Spatie)                                 |
| `staff.created_at` | `users.created_at` (or `model_has_roles` timestamp if available) |
| `staff.deleted_at` | `users.deleted_at` (Soft Deletes on User model)                  |

---

## Key Tables Involved

### 1. Users Table

- **Column**: `user_type`
- **Value**: `'staff'`
- **Purpose**: Identifies the user as a staff member for high-level authentication gates.

### 2. Roles Table (`roles`)

- **Managed by**: `Spatie\Permission\Models\Role`
- **Typical Roles**:
    - `Super Admin`
    - `Manager`
    - `Editor`
    - `Support`

### 3. Model Has Roles (`model_has_roles`)

- **Purpose**: Links a `User` (`model_id`) to a `Role` (`role_id`).

---

## Business Logic

### Creating a Staff Member

```php
// 1. Create User
$user = User::create([
    'name' => 'Staff Name',
    'email' => 'staff@example.com',
    'password' => Hash::make('password'),
    'user_type' => 'staff',
]);

// 2. Assign Role
$user->assignRole('Manager');
```

### Checking Permissions

```php
// Old way (via Staff model)
// $user->staff->role->hasPermission('edit_products');

// New way (Directly on User)
$user->can('edit_products');
```

---

## Migration Notes

Attributes that were previously on the `staff` table (like `phone`, `avatar`) should now be accessed via the `users`
table columns. If specific staff-only attributes existed that don't fit in `users`, they should be migrated to a generic
`user_meta` table or added to `users` if appropriate.

**Former Staff Columns**:

- `role_id`: Moved to `model_has_roles`.
- `user_id`: Redundant (is the `users.id`).

---

## Related Documentation

- [Users Table](./users_table_reference.md)
- [Roles Table](./roles_table_reference.md)
- [Permissions Table](./permissions_table_reference.md)
