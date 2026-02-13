# Coupons Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `coupons`  
**Engine**: InnoDB

---

## Overview

The `coupons` table stores discount codes that customers can apply at checkout. Coupons can be flat-rate or
percentage-based and can be restricted to specific products or cart totals.

**Related Tables**:

- `users`: The seller who created the coupon.
- `carts`, `orders`: Where coupons are used.

---

## Column Specifications

### **Primary Key**

#### `id` - Coupon ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Coupon Definition**

#### `type` - Scope

```sql
varchar(255) NOT NULL
```

- **Values**: `'cart_base'`, `'product_base'`.
- **Purpose**: Determines if discount applies to total cart or specific items.

#### `code` - The Code

```sql
varchar(255) NOT NULL
```

- **Purpose**: The string customer enters (e.g., "SAVE10").

#### `details` - Restrictions

```sql
longtext NOT NULL
```

- **Purpose**: JSON data defining rules (e.g., min purchase amount, specific product IDs).

---

### **Value**

#### `discount` - Amount

```sql
double(20,2) NOT NULL
```

- **Purpose**: The numeric value of the discount.

#### `discount_type` - Unit

```sql
varchar(100) NOT NULL
```

- **Values**: `'amount'`, `'percent'`.

---

### **Validity**

#### `start_date`, `end_date`

```sql
int(15) DEFAULT NULL
```

- **Purpose**: Unix timestamps for validity period.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
