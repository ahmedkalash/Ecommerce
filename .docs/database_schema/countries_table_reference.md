# Countries Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `countries`  
**Engine**: InnoDB

---

## Overview

The `countries` table stores the list of supported countries for the application. It is the top level of the location
hierarchy.

**Related Tables**:

- `states`: Child states/regions.
- `addresses`: User addresses.

---

## Column Specifications

### **Primary Key**

#### `id` - Country ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Details**

#### `code` - ISO Code

```sql
varchar(2) NOT NULL
```

- **Example**: `US`, `IN`, `NG`.

#### `name` - Country Name

```sql
varchar(100) NOT NULL
```

#### `zone_id` - Shipping Zone

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Optional link to a shipping zone (if zone-based shipping is enabled).

---

### **Status**

#### `status` - Active Status

```sql
int(1) NOT NULL DEFAULT 1
```

- **Values**: `1` (Active/Enabled), `0` (Disabled).

---

### **Timestamps**

#### `created_at`, `updated_at`, `deleted_at`

```sql
timestamp
DEFAULT NULL
```

- **Note**: Supports soft deletes (`deleted_at`).
