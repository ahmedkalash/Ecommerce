# Commission Histories Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `commission_histories`  
**Engine**: InnoDB

---

## Overview

The `commission_histories` table logs the financial split between the platform admin and the seller for each item sold.
It is created when an order is placed/confirmed.

**Related Tables**:

- `orders`: Parent order.
- `order_details`: Specific item sold.
- `users`: Seller involved.

---

## Column Specifications

### **Primary Key**

#### `id` - History ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Order Context**

#### `order_id` - Order

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `orders.id`.

#### `order_detail_id` - Item

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `order_details.id`.
- **Granularity**: Commissions are calculated *per item*, not just per order.

#### `seller_id` - Payee

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `users.id` (seller).

---

### **Financials**

#### `admin_commission` - Platform Fee

```sql
double(25, 2) NOT NULL DEFAULT 0.00
```

- **Purpose**: The amount retained by the platform owner.
- **Example**: 10% of product price.

#### `seller_earning` - Seller Revenue

```sql
double(25, 2) NOT NULL DEFAULT 0.00
```

- **Purpose**: The amount payable to the seller.
- **Formula**: `(Product Price * Qty) - Admin Commission + Tax + Shipping`.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```

---

## Relationships

### Belongs To Order

```php
public function order()
{
    return $this->belongsTo(Order::class);
}
```

### Belongs To Seller

```php
public function seller()
{
    return $this->belongsTo(User::class, 'seller_id');
}
```
