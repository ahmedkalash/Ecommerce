# Cities Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `cities`  
**Engine**: InnoDB

---

## Overview

The `cities` table defines the third level of the location hierarchy. Shipping costs can often be defined at this
granular level.

**Related Tables**:

- `states`: Parent state.
- `countries`: Parent country (denormalized for query speed).

---

## Column Specifications

### **Primary Key**

#### `id` - City ID

```sql
bigint(20) NOT NULL auto_increment primary key
```

---

### **Details**

#### `name` - City Name

```sql
varchar(255) NOT NULL
```

#### `state_id` - State

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `states.id`.

#### `country_id` - Country

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `countries.id`.

---

### **Shipping**

#### `cost` - Shipping Cost

```sql
double(20,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Specific shipping cost to this city (overrides general rules if configured).

---

### **Status**

#### `status` - Active Status

```sql
int(11) NOT NULL DEFAULT 1
```

---

### **Timestamps**

#### `created_at`, `updated_at`, `deleted_at`

```sql
timestamp DEFAULT NULL
```
