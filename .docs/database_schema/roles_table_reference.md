# Roles Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `roles`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `roles` table is part of the **Laravel Spatie Permission** package integration and defines authorization roles for the multi-tenant e-commerce platform. It works in conjunction with the `permissions` table to implement **Role-Based Access Control (RBAC)**.

**Key Concept**: Roles are *collections of permissions*. Instead of assigning individual permissions to each user, you assign them a role (e.g., "Admin", "Staff", "Super Admin"), and that role comes with a predefined set of permissions.

**Related Tables**:
- `permissions`: Defines individual permissions (e.g., "users.create", "products.delete")
- `role_has_permissions`: Pivot table linking roles to permissions (many-to-many)
- `model_has_roles`: Pivot table linking users to roles (polymorphic many-to-many)
- `users`: Users who are assigned roles

---

## Column Specifications

### **Primary Key**

#### `id` - Role ID
```sql
bigint(20) unsigned NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for each role
- **Type**: Unsigned big integer
- **Usage**: Referenced in `role_has_permissions` and `model_has_roles` tables

**Example Usage**:
```php
$role = Role::find(1); // Super Admin
$role->permissions; // Get all permissions for this role
```

---

### **Core Columns**

#### `name` - Role Name
```sql
varchar(191) NOT NULL
```
- **Purpose**: Human-readable name of the role
- **Length**: 191 characters (indexed string limit for utf8mb4)
- **Uniqueness**: Must be unique per `guard_name` (enforced by Spatie package

)

**Common Values**:
- `Super Admin` - Full system access, bypasses all permission checks
- `Admin` - Administrative staff with most permissions
- `Staff` - Limited administrative access
- `Seller` - Vendor role with shop management permissions
- `Customer Support` - Support team role

**Usage**:
```php
// Check if user has role
if ($user->hasRole('Super Admin')) {
    // User is super admin
}

// Assign role to user
$user->assignRole('Staff');
```

**⚠️ Important**: Role names are **case-sensitive**. `'Admin'` and `'admin'` are treated as different roles.

---

#### `guard_name` - Authentication Guard
```sql
varchar(191) NOT NULL
```
- **Purpose**: Specifies which authentication guard this role belongs to
- **Default Value**: `'web'` (in this project, only web guard is used)
- **Multi-Guard Support**: Allows different role sets for different auth contexts

**Current System**:
```php
'web' => Regular session-based authentication
```

**Future Extensibility**:
```php
'admin' => Separate admin guard (if implemented)
'api'   => API token authentication (if implemented)
```

**Why This Exists**: The Spatie Permission package supports applications with multiple authentication guards (e.g., separate admin and user login systems). This project currently only uses `'web'`.

**Usage**:
```php
// Create role for specific guard
Role::create(['name' => 'Editor', 'guard_name' => 'web']);

// Check role for specific guard
$user->hasRole('Admin', 'web');
```

---

### **Timestamps**

#### `created_at` - Creation Timestamp
```sql
timestamp NULL DEFAULT NULL
```
- **Purpose**: When the role was created
- **Managed By**: Laravel automatically sets this
- **Nullable**: Yes (Laravel default)

---

#### `updated_at` - Last Update Timestamp
```sql
timestamp NULL DEFAULT NULL
```
- **Purpose**: When the role was last modified
- **Managed By**: Laravel automatically updates this
- **Usage**: Track when role permissions were changed

---

## Indexes & Constraints

### Primary Key
```sql
PRIMARY KEY (id)
```

### Unique Constraints
The Spatie Permission package typically adds:
```sql
UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
```
- **Purpose**: Ensures role names are unique per guard
- **Example**: Can't have two "Admin" roles for the "web" guard

---

## Common Roles in the System

Based on the codebase analysis:

| Role Name | Typical Permissions | Used By |
|-----------|-------------------|---------|
| **Super Admin** | All permissions (bypasses checks via Gate::before) | System owner |
| **Admin** | Most permissions except system config | Admin staff |
| **Staff** | Limited admin permissions | Support team, content managers |
| **Seller** | Shop management, product CRUD, order fulfillment | Vendors |
| **Customer Support** | View orders, manage tickets | Support team |

---

## Relationships

### Has Many Permissions (via Pivot)
```php
// In Role model
public function permissions()
{
    return $this->belongsToMany(Permission::class, 'role_has_permissions');
}
```

**Usage**:
```php
$role = Role::findByName('Admin');
$role->permissions; // Collection of Permission models
$role->givePermissionTo('products.create');
$role->revokePermissionTo('users.delete');
```

### Assigned to Users (Polymorphic via Pivot)
```php
// In Role model
public function users()
{
    return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
}
```

**Usage**:
```php
$role = Role::findByName('Staff');
$role->users; // All users with this role
```

---

## Spatie Permission Package Integration

This table is managed by the **Spatie Laravel-Permission** package. Key features:

### Permission Checking
```php
// Direct permission check
$user->can('products.create');

// Role-based check
$user->hasRole('Admin');

// Role OR permission check
$user->hasAnyRole(['Admin', 'Staff']);
$user->hasPermissionTo('products.edit');
```

### Middleware
```php
// Protect routes
Route::middleware(['role:Admin'])->group(function () {
    // Admin only routes
});

Route::middleware(['permission:products.create'])->group(function () {
    // Users with this permission
});
```

### Gate Bypass (Super Admin)
In `AuthServiceProvider.php`:
```php
Gate::before(function ($user, $ability) {
    return $user->hasRole('Super Admin') ? true : null;
});
```
**Effect**: Users with "Super Admin" role bypass ALL permission checks.

---

## Security Considerations

### 1. Super Admin Protection
**Issue**: Super Admin bypass is powerful but risky.

**Recommendation**:
```php
// Add IP whitelist or 2FA requirement
Gate::before(function ($user, $ability) {
    if ($user->hasRole('Super Admin')) {
        // Check if from trusted IP
        if (!in_array(request()->ip(), config('app.admin_ips'))) {
            return false;
        }
        return true;
    }
    return null;
});
```

### 2. Role Assignment Audit
**Missing**: No audit trail for role changes

**Recommendation**: Log role assignments/revocations
```php
// When assigning role
Log::info('Role assigned', [
    'user_id' => $user->id,
    'role' => $roleName,
    'assigned_by' => auth()->id(),
]);
```

### 3. Prevent Role Escalation
**Risk**: Staff users could assign themselves Admin role if not protected

**Protection**:
```php
// In controller
$this->authorize('assign-roles', $user);
```

---

## Common Queries

### Get All Users with a Role
```php
$admins = User::role('Admin')->get();
```

### Get All Permissions for a Role
```php
$role = Role::findByName('Staff');
$permissions = $role->permissions->pluck('name');
```

### Sync Role Permissions
```php
$role = Role::findByName('Editor');
$role->syncPermissions(['products.create', 'products.edit', 'products.view']);
```

### Check if Role Has Permission
```php
$role = Role::findByName('Seller');
if ($role->hasPermissionTo('products.delete')) {
    // Role can delete products
}
```

---

## Refactoring Opportunities

### 1. Add Role Hierarchy
**Current**: Flat role structure, no inheritance

**Proposed**:
```sql
ALTER TABLE roles ADD COLUMN parent_role_id BIGINT UNSIGNED NULL;
ALTER TABLE roles ADD CONSTRAINT fk_parent_role 
FOREIGN KEY (parent_role_id) REFERENCES roles(id) ON DELETE SET NULL;
```

**Benefit**: "Admin" role could inherit all "Staff" permissions

### 2. Add Role Metadata
**Current**: Only `name` and `guard_name`

**Proposed**:
```sql
ALTER TABLE roles ADD COLUMN description TEXT NULL;
ALTER TABLE roles ADD COLUMN level INT DEFAULT 0;
ALTER TABLE roles ADD COLUMN is_system_role TINYINT(1) DEFAULT 0;
```

**Benefits**:
- `description`: Document what the role is for
- `level`: Numerical hierarchy (higher = more powerful)
- `is_system_role`: Prevent deletion of core roles

### 3. Add Scope to Roles
**Issue**: All roles are global; no shop-specific or category-specific roles

**Proposed**:
```sql
ALTER TABLE roles ADD COLUMN scope_type VARCHAR(50) NULL; -- 'global', 'shop', 'category'
ALTER TABLE roles ADD COLUMN scope_id INT NULL;
```

**Use Case**: Allow shop owners to create custom roles for their staff

---

## Related Documentation

- [Permissions Table](./permissions_table_reference.md)
- [Users Table](./users_table_reference.md)
- [Auth Module Analysis](../Auth_Module_Analysis.md)
- [Spatie Laravel-Permission Documentation](https://spatie.be/docs/laravel-permission/)
