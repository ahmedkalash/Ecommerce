# Product Stocks Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `product_stocks`  
**Engine**: InnoDB

---

## Overview

The `product_stocks` table is the inventory source of truth for **Variant Products**. It maps a specific combination of
options (e.g., "Size: M, Color: Blue") to a specific stock quantity and price impact.

**Key Concept**:

- **Simple Products**: Manage stock directly in `products.current_stock`.
- **Variant Products**: Manage stock in `product_stocks` table. The sum of `qty` here should typically match
  `products.current_stock`.

**Related Tables**:

- `products`: Parent product.

---

## Column Specifications

### **Primary Key**

#### `id` - Stock ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Stock Identity**

#### `product_id` - Parent Product

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `products.id`.

#### `variant` - Variant Key

```sql
varchar(255) NOT NULL
```

- **Purpose**:  Unique string identifier for the combination.
- **Format**: Concatenated attribute values (e.g., `M-Blue`, `128GB-Black`).
- **Usage**: Matched against user selection to find price/stock.

#### `sku` - Stock Keeping Unit

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Merchant's unique code for this variant.
- **Usage**: Inventory tracking, barcode generation.

---

### **Pricing & Inventory**

#### `price` - Variant Price

```sql
double(20,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: The selling price for this specific variant.
- **Overrides**: This price *replaces* the base `products.unit_price`.
- **Note**: Should use `DECIMAL` type for precision.

#### `qty` - Quantity on Hand

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Current available stock for this variant.
- **Logic**: Decrements on order, increments on restock/cancellation.

---

### **Media**

#### `image` - Variant Image

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Upload ID for an image specific to this variant.
- **Usage**: When user selects "Blue", the main product image updates to this image.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
