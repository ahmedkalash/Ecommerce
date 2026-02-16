# Taxes Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `taxes`  
**Engine**: InnoDB

---

## Overview

The `taxes` table defines the global tax rules available in the system. These can be enabled/disabled and applied to
products.

**Related Tables**:

- `product_taxes`: Links specific tax rates to products.

---

## Column Specifications

### **Primary Key**

#### `id` - Tax ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Tax Definition**

#### `name` - Tax Name

```sql
varchar(255) NOT NULL
```

- **Purpose**: Name of the tax (e.g., "VAT", "GST", "Sales Tax").

#### `tax_status` - Active State

```sql
tinyint(1) NOT NULL DEFAULT 1
```

- **Values**:
    - `0`: Inactive.
    - `1`: Active (Available for selection).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
