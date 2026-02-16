# States Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `states`  
**Engine**: InnoDB

---

## Overview

The `states` table defines the second level of the location hierarchy (Regions/Provinces/States).

**Related Tables**:

- `countries`: Parent country.
- `cities`: Child cities.

---

## Column Specifications

### **Primary Key**

#### `id` - State ID

```sql
bigint(20) NOT NULL auto_increment primary key
```

---

### **Details**

#### `name` - State Name

```sql
varchar(255) NOT NULL
```

#### `country_id` - Country

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `countries.id`.

---

### **Status**

#### `status` - Active Status

```sql
int(11) NOT NULL DEFAULT 0
```

- **Values**: `1` (Active), `0` (Inactive).

---

### **Timestamps**

#### `created_at`, `updated_at`, `deleted_at`

```sql
timestamp
DEFAULT NULL
```

- **Note**: Supports soft deletes.
