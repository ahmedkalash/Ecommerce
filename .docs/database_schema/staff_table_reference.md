# Staff Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `staff`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `staff` table stores extended data for administrative staff members in the e-commerce platform. It acts as a **profile extension table** for users with `user_type = 'staff'` in the `users` table.

**Architecture Pattern**: **Single Table Inheritance (STI) Extension**
- Main user data → `users` table
- Staff-specific data → `staff` table (this table)
- Linked via `user_id` foreign key

**Related Tables**:
- `users`: Contains authentication and basic profile data
- `roles`: Staff members are assigned roles via Spatie Permission package
- `pickup_points`: Some staff manage pickup locations (linked via `staff_id`)

---

## Column Specifications

### **Primary Key**

#### `id` - Staff Profile ID
```sql
int(11) NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for the staff profile record
- **Type**: Auto-incrementing integer
- **Usage**: Referenced in `pickup_points` table

**Note**: This is NOT the user ID. The staff's user ID is stored in `user_id`.

---

### **Foreign Keys**

#### `user_id` - Link to Users Table
```sql
int(11) NOT NULL
```
- **Purpose**: Foreign key to `users.id`
- **Constraint**: Should reference a user where `user_type = 'staff'`
- **Uniqueness**: One user can have only ONE staff profile (1:1 relationship)

**Business Rule**:
```php
// When creating staff
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'user_type' => 'staff', // Required
]);

$staff = Staff::create([
    'user_id' => $user->id,
    'role_id' => $adminRole->id,
]);
```

**⚠️ Critical Issue**: No database-level foreign key constraint or unique constraint on `user_id`.

**Recommended Fix**:
```sql
ALTER TABLE staff 
ADD CONSTRAINT fk_staff_user 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE staff 
ADD UNIQUE KEY unique_user_id (user_id);
```

---

#### `role_id` - Assigned Role
```sql
int(11) NOT NULL
```
- **Purpose**: Foreign key to `roles.id` (Spatie Permission package)
- **Usage**: Defines the staff member's permissions and access level

**Common Staff Roles**:
```
Admin         => Full access to admin panel
Staff         => Limited administrative access
Customer Support  => Order management, customer queries
Content Manager   => Blog, pages, slider management
Product Manager   => Product approval, category management
```

**Usage**:
```php
$staff = Staff::find(1);
$staff->role; // Get the Role model
$staff->user->hasRole('Admin'); // Check via user relationship
```

**⚠️ Issue**: No foreign key constraint to `roles` table.

**Recommended Fix**:
```sql
ALTER TABLE staff 
ADD CONSTRAINT fk_staff_role 
FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT;
```

---

### **Timestamps**

#### `created_at` - Profile Creation
```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
- **Purpose**: When the staff profile was created (when user joined as staff)
- **Usage**: Track when team members were added

**Note**: This may differ from `users.created_at` if a customer was promoted to staff.

---

#### `deleted_at` - Soft Delete Timestamp
```sql
timestamp NULL DEFAULT NULL
```
- **Purpose**: Implements soft deletes - marks when staff was removed without deleting data
- **NULL**: Staff is active
- **Non-NULL**: Staff was deactivated/removed

**Usage with Laravel Soft Deletes**:
```php
// In Staff model
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
}

// Soft delete a staff
$staff->delete(); // Sets deleted_at to current timestamp

// Query only active staff
$activeStaff = Staff::all(); // Excludes soft-deleted

// Include soft-deleted
$allStaff = Staff::withTrashed()->get();

// Only soft-deleted
$removedStaff = Staff::onlyTrashed()->get();

// Restore soft-deleted staff
$staff->restore();

// Permanent delete
$staff->forceDelete();
```

**Why Soft Deletes**:
- Maintain audit trail
- Preserve references in `pickup_points` table
- Allow restoration if staff member returns

---

## Relationships

### Belongs To User
```php
// In Staff model
public function user()
{
    return $this->belongsTo(User::class);
}
```

**Usage**:
```php
$staff = Staff::find(1);
$staff->user->name; // Get staff member's name
$staff->user->email; // Get staff member's email
```

---

### Belongs To Role
```php
// In Staff model
public function role()
{
    return $this->belongsTo(Role::class);
}
```

**Usage**:
```php
$staff = Staff::find(1);
$staff->role->name; // e.g., "Admin"
$staff->role->permissions; // All permissions for this role
```

---

### Has User Relationship (Inverse)
```php
// In User model
public function staff()
{
    return $this->hasOne(Staff::class);
}
```

**Usage**:
```php
$user = User::find(1);
if ($user->staff) {
    // User is a staff member
    $roleId = $user->staff->role_id;
}
```

---

## Business Logic

### Staff Creation Flow
```php
// 1. Create User
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'user_type' => 'staff', // Critical
]);

// 2. Create Staff Profile
$staff = Staff::create([
    'user_id' => $user->id,
    'role_id' => $request->role_id,
]);

// 3. Assign Role (Spatie Permission)
$user->assignRole(Role::find($request->role_id)->name);

return $staff;
```

### Staff Permission Checking
```php
// Check if user is staff
if (auth()->user()->user_type == 'staff') {
    // User is staff
}

// Check staff permissions
if (auth()->user()->can('products.approve')) {
    // Staff has this permission via their role
}

// Get staff's role
$staff = auth()->user()->staff;
$roleName = $staff->role->name;
```

### Staff Dashboard Access
```php
// Middleware check
public function handle($request, Closure $next)
{
    if (!in_array(auth()->user()->user_type, ['admin', 'staff'])) {
        abort(403, 'Admin access only');
    }
    
    return $next($request);
}
```

---

## Common Queries

### Get All Active Staff
```php
$staff = Staff::with('user', 'role')->get();
```

### Get Staff by Role
```php
$admins = Staff::where('role_id', $adminRoleId)->get();
```

### Get Staff with User and Role Info
```php
$staff = Staff::with(['user', 'role.permissions'])
    ->whereHas('user', function ($query) {
        $query->where('banned', 0);
    })
    ->get();
```

### Count Staff Members
```php
$totalStaff = Staff::count();
$activeStaff = Staff::whereHas('user', fn($q) => $q->where('banned', 0))->count();
```

---

## Security Considerations

### 1. Prevent User Type Mismatch
**Issue**: Staff row might exist but `users.user_type != 'staff'`

**Validation**:
```php
// Before creating staff
if ($user->user_type !== 'staff') {
    throw new \Exception('User must have user_type = staff');
}
```

**Database Trigger** (Alternative):
```sql
DELIMITER $$
CREATE TRIGGER check_staff_user_type 
BEFORE INSERT ON staff
FOR EACH ROW
BEGIN
    DECLARE user_type_check VARCHAR(20);
    SELECT user_type INTO user_type_check FROM users WHERE id = NEW.user_id;
    IF user_type_check != 'staff' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User must be of type staff';
    END IF;
END$$
DELIMITER ;
```

### 2. Role Assignment Validation
**Issue**: Staff could be assigned non-existent role

**Protection**:
```php
// In Staff model
public static function boot()
{
    parent::boot();
    
    static::creating(function ($staff) {
        if (!Role::find($staff->role_id)) {
            throw new \Exception('Invalid role_id');
        }
    });
}
```

### 3. Audit Soft Deletes
**Issue**: No record of WHO removed a staff member

**Solution**: Add `deleted_by` column
```sql
ALTER TABLE staff ADD COLUMN deleted_by INT NULL;
ALTER TABLE staff ADD CONSTRAINT fk_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id);
```

```php
// Override delete method
public function delete()
{
    $this->deleted_by = auth()->id();
    $this->save();
    
    return parent::delete();
}
```

---

## Known Issues

### 1. Missing Foreign Key Constraints
**Risk**: Orphaned staff records if user is deleted

**Impact**: Staff records reference non-existent users
```sql
-- Find orphaned records
SELECT s.* FROM staff s 
LEFT JOIN users u ON s.user_id = u.id 
WHERE u.id IS NULL;
```

### 2. No Unique Constraint on user_id
**Risk**: One user could have multiple staff profiles

**Detection**:
```sql
-- Find duplicate user_ids
SELECT user_id, COUNT(*) 
FROM staff 
GROUP BY user_id 
HAVING COUNT(*) > 1;
```

### 3. Role Change Not Synced
**Issue**: `staff.role_id` might not match user's actual assigned role in `model_has_roles`

**Audit Query**:
```sql
SELECT s.id, s.user_id, s.role_id as staff_role, mhr.role_id as actual_role
FROM staff s
LEFT JOIN model_has_roles mhr ON mhr.model_id = s.user_id AND mhr.model_type = 'App\\Models\\User'
WHERE s.role_id != mhr.role_id OR mhr.role_id IS NULL;
```

---

## Refactoring Opportunities

### 1. Add Missing Constraints
```sql
-- Foreign keys
ALTER TABLE staff ADD CONSTRAINT fk_staff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE staff ADD CONSTRAINT fk_staff_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT;

-- Unique constraint
ALTER TABLE staff ADD UNIQUE KEY unique_staff_user (user_id);
```

### 2. Add Metadata Columns
```sql
ALTER TABLE staff ADD COLUMN department VARCHAR(100) NULL;
ALTER TABLE staff ADD COLUMN employee_id VARCHAR(50) NULL;
ALTER TABLE staff ADD COLUMN hire_date DATE NULL;
ALTER TABLE staff ADD COLUMN salary DECIMAL(10,2) NULL;
ALTER TABLE staff ADD COLUMN notes TEXT NULL;
```

### 3. Sync Role Change Event
```php
// Listener for role changes
Event::listen(StaffRoleChanged::class, function ($event) {
    $staff = $event->staff;
    $newRole = Role::find($event->newRoleId);
    
    // Update staff table
    $staff->role_id = $newRole->id;
    $staff->save();
    
    // Sync Spatie role
    $staff->user->syncRoles([$newRole->name]);
});
```

### 4. Consider Merging with Users Table
**Question**: Does the `staff` table add enough value to justify a separate table?

**Current State**: Only stores `role_id`

**Alternative**: Add `staff_role_id` column directly to `users` table
```sql
ALTER TABLE users ADD COLUMN staff_role_id INT NULL;
ALTER TABLE users ADD CONSTRAINT fk_user_staff_role FOREIGN KEY (staff_role_id) REFERENCES roles(id);
```

**Benefits**:
- Simpler queries (no JOIN needed)
- Fewer tables to maintain
- Eliminates potential sync issues

**Trade-offs**:
- Less normalized
- Users table grows wider

---

## Related Documentation

- [Users Table](./users_table_reference.md)
- [Roles Table](./roles_table_reference.md)
- [Permissions Table](./permissions_table_reference.md)
- [Auth Module Analysis](../Auth_Module_Analysis.md)
