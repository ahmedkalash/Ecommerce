# Permissions Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `permissions`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `permissions` table stores individual permission definitions as part of the **Laravel Spatie Permission** package. Each permission represents a specific action that can be performed in the system (e.g., `products.create`, `orders.view`, `users.delete`).

**Key Concept**: Permissions are granular capabilities. They are assigned to **roles**, and users inherit permissions through their assigned roles.

**Permission Pattern**: This project uses **dot notation** for permission names:
```
{resource}.{action}
```
Examples: `products.create`, `sellers.approve`, `reports.download`

**Related Tables**:
- `roles`: Collections of permissions
- `role_has_permissions`: Pivot table linking roles to permissions
- `model_has_permissions`: Direct user-to-permission assignments (bypassing roles)

---

## Column Specifications

### **Primary Key**

#### `id` - Permission ID
```sql
bigint(20) unsigned NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for each permission
- **Type**: Unsigned big integer
- **Usage**: Referenced in pivot tables for role and user assignments

---

### **Core Columns**

#### `name` - Permission Name
```sql
varchar(191) NOT NULL
```
- **Purpose**: Unique identifier for the permission (used in code)
- **Format**: Typically `{resource}.{action}` (dot notation)
- **Length**: 191 characters (utf8mb4 indexed string limit)
- **Uniqueness**: Must be unique per `guard_name`

**Common Naming Patterns**:
```
# CRUD Operations
products.create
products.view
products.edit
products.delete

# Approval/Moderation
sellers.approve
sellers.reject
products_refunds.approve

# Reports & Analytics
reports.view
reports.download
analytics.admin_commission

# Configuration
settings.general
settings.payment_methods
settings.email_templates

# User Management
users.ban
users.view_profile
staff.create
```

**Reserved Permissions**:
- Permissions starting with `admin.` are typically admin-only
- `*` wildcard is NOT supported by default in Spatie Permission

**Usage**:
```php
// Check permission
if ($user->can('products.delete')) {
    $product->delete();
}

// Assign permission to role
$role = Role::findByName('Editor');
$role->givePermissionTo('products.create');

// Direct assignment to user
$user->givePermissionTo('special.access');
```

---

#### `section` - Permission Category/Section
```sql
varchar(50) DEFAULT NULL
```
- **Purpose**: Groups related permissions for admin UI organization
- **Nullable**: Yes
- **Usage**: Display permissions in organized sections in the admin panel

**Common Sections**:
```php
'products'          => Product management permissions
'orders'            => Order and fulfillment permissions
'users'             => User and staff management
'sellers'           => Vendor/seller management
'settings'          => System configuration
'reports'           => Analytics and reports
'marketing'         => Coupons, flash deals, campaigns
'content'           => Pages, blogs, sliders
'support'           => Conversations, reviews, refunds
```

**Example in Admin UI**:
```php
// Group permissions by section for display
$permissions = Permission::all()->groupBy('section');

// Render checkboxes per section
foreach ($permissions as $section => $sectionPermissions) {
    echo "<h3>{$section}</h3>";
    foreach ($sectionPermissions as $permission) {
        echo "<input type='checkbox' name='permissions[]' value='{$permission->name}'>";
        echo "<label>{$permission->name}</label>";
    }
}
```

**⚠️ Issue**: NULL section means ungrouped permissions appear at bottom of admin UI.

---

#### `guard_name` - Authentication Guard
```sql
varchar(191) NOT NULL
```
- **Purpose**: Specifies which authentication guard this permission belongs to
- **Default**: `'web'` (this project uses only web guard)
- **Multi-Guard Support**: Same as roles table

**Usage**:
```php
Permission::create([
    'name' => 'products.delete',
    'section' => 'products',
    'guard_name' => 'web'
]);
```

---

### **Timestamps**

#### `created_at` - Creation Timestamp
```sql
timestamp NULL DEFAULT current_timestamp()
```
- **Purpose**: When the permission was created
- **Managed By**: Laravel (auto-set on insert)
- **Usage**: Track when new permissions were added to the system

---

#### `updated_at` - Last Update Timestamp
```sql
timestamp NULL DEFAULT NULL
```
- **Purpose**: When the permission was last modified
- **Managed By**: Laravel (auto-updated)
- **Usage**: Rarely changes (permission names should be stable)

---

## Indexes & Constraints

### Primary Key
```sql
PRIMARY KEY (id)
```

### Unique Constraints
Spatie Permission package creates:
```sql
UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
```
- **Purpose**: Prevent duplicate permission names per guard
- **Example**: Can't create two `products.delete` permissions for `web` guard

---

## Permission Seeding

Permissions are typically seeded during installation or migrations:

```php
// database/seeders/PermissionSeeder.php
Permission::create(['name' => 'products.create', 'section' => 'products', 'guard_name' => 'web']);
Permission::create(['name' => 'products.edit', 'section' => 'products', 'guard_name' => 'web']);
Permission::create(['name' => 'products.delete', 'section' => 'products', 'guard_name' => 'web']);
Permission::create(['name' => 'products.view', 'section' => 'products', 'guard_name' => 'web']);

Permission::create(['name' => 'sellers.approve', 'section' => 'sellers', 'guard_name' => 'web']);
Permission::create(['name' => 'sellers.reject', 'section' => 'sellers', 'guard_name' => 'web']);
Permission::create(['name' => 'sellers.view', 'section' => 'sellers', 'guard_name' => 'web']);
```

---

## Relationships

### Belongs to Many Roles (via Pivot)
```php
// In Permission model
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_has_permissions');
}
```

**Usage**:
```php
$permission = Permission::findByName('products.delete');
$permission->roles; // All roles that have this permission

// Assign permission to role
$permission->assignRole('Admin');
```

### Assigned to Users Directly (Polymorphic via Pivot)
```php
// In Permission model
public function users()
{
    return $this->morphedByMany(User::class, 'model', 'model_has_permissions', 'permission_id', 'model_id');
}
```

**Use Case**: Give a specific user a permission without changing their role
```php
$permission = Permission::findByName('special.debug_mode');
$permission->users; // Users with direct permission assignment
```

---

## Common Permission Patterns

### Resource-Based Permissions
```php
// Full CRUD
products.create
products.view
products.edit
products.delete

// Extended actions
products.publish
products.feature
products.approve
```

### Feature-Based Permissions
```php
// Flash Sale Management
flash_deals.create
flash_deals.activate

// Commission Management
commissions.calculate
commissions.export

// Report Access
reports.sales
reports.seller_earnings
reports.stock_alert
```

### Scope-Based Permissions
```php
// Own vs All
products.edit_own         // Edit only own products
products.edit_all         // Edit any product

orders.view_own           // View own shop orders
orders.view_all           // View all orders (admin)
```

---

## Permission Checking in Code

### Controller
```php
public function destroy(Product $product)
{
    $this->authorize('delete', $product); // Uses ProductPolicy
    
    // Alternative direct check
    if (!auth()->user()->can('products.delete')) {
        abort(403);
    }
    
    $product->delete();
}
```

### Blade Templates
```blade
@can('products.create')
    <a href="{{ route('products.create') }}">Create Product</a>
@endcan

@cannot('products.delete')
    <p>You cannot delete products</p>
@endcannot
```

### Middleware
```php
// Route protection
Route::middleware(['permission:products.delete'])->group(function () {
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});

// Multiple permissions (OR logic)
Route::middleware(['permission:products.edit|products.delete'])->group(function () {
    // User needs EITHER permission
});

// Multiple permissions (AND logic)
Route::middleware(['permission:products.edit,products.delete'])->group(function () {
    // User needs BOTH permissions
});
```

---

## Security Considerations

### 1. Super Admin Bypass
**Current Behavior**: Super Admin role bypasses ALL permission checks

**Risk**: Even if Super Admin doesn't have a specific permission in DB, they can still perform the action

**Mitigation**:
```php
// In AuthServiceProvider
Gate::before(function ($user, $ability) {
    if ($user->hasRole('Super Admin')) {
        // Still log what they're doing
        Log::info('Super Admin bypass', ['user' => $user->id, 'ability' => $ability]);
        return true;
    }
    return null;
});
```

### 2. Permission Naming Collisions
**Issue**: Typos or inconsistent naming leads to broken authorization

**Best Practice**:
- Use constants or ENUMs for permission names
- Centralize permission definitions

```php
// app/Enums/Permission.php
enum Permission: string
{
    case PRODUCTS_CREATE = 'products.create';
    case PRODUCTS_DELETE = 'products.delete';
    case SELLERS_APPROVE = 'sellers.approve';
}

// Usage
$user->can(Permission::PRODUCTS_DELETE->value);
```

### 3. Missing Permission Checks
**Issue**: Developer forgets to check permission in controller

**Detection**: Use static analysis or custom Artisan command
```php
// Check all routes for missing middleware
php artisan route:list --middleware
```

---

## Common Queries

### Get All Permissions for a Section
```php
$productPermissions = Permission::where('section', 'products')->get();
```

### Get Users with Specific Permission
```php
// Via role
$users = User::permission('products.delete')->get();

// Direct assignment only
$users = User::whereHas('permissions', function ($query) {
    $query->where('name', 'special.access');
})->get();
```

### Sync Permissions for a Role
```php
$role = Role::findByName('Editor');
$role->syncPermissions([
    'products.create',
    'products.edit',
    'products.view'
]);
```

### Check if  Permission Exists
```php
if (Permission::where('name', 'products.delete')->exists()) {
    // Permission defined
}
```

---

## Refactoring Opportunities

### 1. Add Permission Descriptions
**Current**: Only `name` and `section`

**Proposed**:
```sql
ALTER TABLE permissions ADD COLUMN description VARCHAR(255) NULL;
ALTER TABLE permissions ADD COLUMN is_dangerous TINYINT(1) DEFAULT 0;
```

**Benefits**:
- `description`: Explain what the permission allows in admin UI
- `is_dangerous`: Flag destructive permissions (delete, ban, etc.) with extra warnings

### 2. Add Permission Dependencies
**Current**: No way to express "permission A requires permission B"

**Proposed**: Create `permission_dependencies` table
```sql
CREATE TABLE permission_dependencies (
    permission_id BIGINT UNSIGNED NOT NULL,
    depends_on_permission_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (permission_id) REFERENCES permissions(id),
    FOREIGN KEY (depends_on_permission_id) REFERENCES permissions(id)
);
```

**Use Case**: `products.delete` should require `products.view`

### 3. Add Audit Trail for Permission Changes
**Current**: No log of who assigned/revoked permissions

**Proposed**: Event listeners
```php
// In EventServiceProvider
Event::listen(PermissionAssigned::class, LogPermissionChange::class);
Event::listen(PermissionRevoked::class, LogPermissionChange::class);
```

### 4. Group Permissions into Permission Sets
**Current**: Assigning individual permissions is tedious

**Proposed**: Create `permission_sets` table
```sql
CREATE TABLE permission_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    permissions JSON NOT NULL
);
```

**Use Case**: "Product Manager" set = [products.create, products.edit, products.view]

---

## Related Documentation

- [Roles Table](./roles_table_reference.md)
- [Users Table](./users_table_reference.md)
- [Model Has Permissions Table](./model_has_permissions_table_reference.md)
- [Role Has Permissions Table](./role_has_permissions_table_reference.md)
- [Auth Module Analysis](../Auth_Module_Analysis.md)
