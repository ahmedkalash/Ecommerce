# Carriers Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `carriers`  
**Engine**: InnoDB

---

## Overview

The `carriers` table stores shipping provider configurations.

**Related Tables**:

- `orders`: May reference a carrier.

---

## Column Specifications

### **Primary Key**

#### `id` - Carrier ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Details**

#### `name` - Provider Name

```sql
varchar(255) NOT NULL
```

- **Examples**: `FedEx`, `DHL`, `UPS`.

#### `logo` - Icon

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Reference to `uploads.id`.

#### `transit_time` - Estimated Time

```sql
varchar(255) NOT NULL
```

- **Example**: `3-5 days`.

#### `free_shipping` - Free Available?

```sql
tinyint(1) NOT NULL DEFAULT 0
```

- **Values**: `1` (Yes), `0` (No).

---

### **Status**

#### `status` - Active

```sql
tinyint(1) NOT NULL DEFAULT 1
```

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
