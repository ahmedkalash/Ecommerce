# Combined Orders Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `combined_orders`  
**Engine**: InnoDB

---

## Overview

The `combined_orders` table groups multiple "sub-orders" (from different sellers) that were placed in a single checkout
session. It serves as the master record for the customer's transaction.

**Key Concept**:

- **Checkout**: A customer buys 3 items from 2 sellers.
- **Combined Order**: 1 record created here.
- **Orders**: 2 records created in `orders` table (one per seller), both linked to this `combined_order_id`.

**Related Tables**:

- `orders`: Child orders linked via `combined_order_id`.

---

## Column Specifications

### **Primary Key**

#### `id` - Combined ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Order Info**

#### `user_id` - Customer

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `users.id`.

#### `grand_total` - Total Amount

```sql
double(20, 2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Sum of all sub-orders' grand totals.

#### `shipping_address` - Address Snapshot

```sql
text
DEFAULT NULL
```

- **Purpose**: JSON or Text representation of the shipping address used.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```

---

## Relationships

### Has Many Orders

```php
public function orders()
{
    return $this->hasMany(Order::class);
}
```
