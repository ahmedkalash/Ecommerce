# Addresses Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `addresses`  
**Engine**: InnoDB

---

## Overview

The `addresses` table stores customer shipping and billing addresses. Users can have multiple addresses, with one set as
default.

**Related Tables**:

- `users`: Owner of the address.
- `countries`, `states`, `cities`: Geographic references.

---

## Column Specifications

### **Primary Key**

#### `id` - Address ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **User & Location**

#### `user_id` - User

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `users.id`.

#### `address` - Street Address

```sql
varchar(255) DEFAULT NULL
```

#### `country_id`, `state_id`, `city_id`

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign keys to respective location tables.

#### `postal_code`

```sql
varchar(255) DEFAULT NULL
```

#### `phone`

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Contact number for delivery.

---

### **Coordinates**

#### `longitude`, `latitude`

```sql
float(17, 15) DEFAULT NULL
```

- **Purpose**: Geolocation for map-based delivery systems.

---

### **Settings**

#### `set_default` - Default Flag

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `0` (No), `1` (Yes).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
