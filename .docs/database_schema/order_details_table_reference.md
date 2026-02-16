# Order Details Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `order_details`  
**Engine**: InnoDB

---

## Overview

The `order_details` table stores the individual line items for each order. It links the order to specific products,
variations, and quantities.

**Related Tables**:

- `orders`: Parent order.
- `products`: Valid product reference.
- `users`: Seller of the product.

---

## Column Specifications

### **Primary Key**

#### `id` - Detail ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Order & Product Link**

#### `order_id` - Parent Order

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `orders.id`.

#### `product_id` - Product

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `products.id`.

#### `seller_id` - Seller

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `users.id` (user_type='seller').
- **Usage**: Used to calculate seller earnings and commissions.

---

### **Item Details**

#### `variation` - Product Attributes

```sql
longtext DEFAULT NULL
```

- **Purpose**: Stores the selected attributes (Size: M, Color: Blue) or the calculated variant string.

#### `quantity` - Quantity

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Number of units purchased.

---

### **Financials**

#### `price` - Unit Price

```sql
double(20,2) DEFAULT NULL
```

- **Purpose**: Price per unit *at the time of purchase*.

#### `tax` - Total Tax

```sql
double(20,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Total tax amount for this line item.

#### `shipping_cost` - Shipping

```sql
double(20,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Shipping fee allocated to this specific item.

#### `earn_point` - Club Points

```sql
double(25,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Loyalty points earned for this item.

---

### **Status Tracking**

#### `payment_status` - Item Payment Status

```sql
varchar(10) NOT NULL DEFAULT 'unpaid'
```

- **Values**: `paid`, `unpaid`.

#### `delivery_status` - Item Delivery Status

```sql
varchar(20) DEFAULT 'pending'
```

- **Values**: `pending`, `confirmed`, `on_delivery`, `delivered`, `cancelled`.
- **Note**: Individual items can have different statuses (e.g., partial shipment).

#### `refund_days` - Refund Window

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Days allowed for refund after delivery.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
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

### Belongs To Product

```php
public function product()
{
    return $this->belongsTo(Product::class);
}
```
