# Pickup Points Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `pickup_points`  
**Engine**: InnoDB

---

## Overview

The `pickup_points` table stores locations where customers can physically collect their orders, often bypassing shipping
costs.

**Related Tables**:

- `orders`: Can select `pickup_point_id` instead of a shipping address.
- `staff`: Often a staff member manages a pickup point (`pickup_point_staffs` often links them).

---

## Column Specifications

### **Primary Key**

#### `id` - Location ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Manager**

#### `user_id` - Point Manager

```sql
bigint(20) unsigned DEFAULT NULL
```

- **Purpose**: Staff member in charge of this point.

---

### **Location Details**

#### `name` - Point Name

```sql
varchar(255) NOT NULL
```

- **Example**: `Downtown Store`, `Warehouse A`.

#### `address` - Full Address

```sql
mediumtext
NOT NULL
```

#### `phone` - Contact

```sql
varchar(15) NOT NULL
```

---

### **Configuration**

#### `pick_up_status` - Available?

```sql
int(1) DEFAULT NULL
```

#### `cash_on_pickup_status` - Payment

```sql
int(1) DEFAULT NULL
```

- **Purpose**: Whether "Cash on Delivery/Pickup" is allowed here.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
