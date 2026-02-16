# Colors Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `colors`  
**Engine**: InnoDB

---

## Overview

The `colors` table serves as a global registry of available colors for products. When adding a product, sellers select
from these predefined colors to create variants.

**Related Tables**:

- `products`: Referenced in `colors` JSON column.
- `attribute_values`: Linking attributes to color codes (legacy usage).

---

## Column Specifications

### **Primary Key**

#### `id` - Color ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Color Definition**

#### `name` - Color Name

```sql
varchar(30) DEFAULT NULL
```

- **Purpose**: Human-readable name (e.g., "Royal Blue", "Crimson").

#### `code` - Hex Code

```sql
varchar(10) DEFAULT NULL
```

- **Purpose**: CSS-compatible hex code (e.g., `#4169E1`).
- **Usage**: Used to render color swatches on the frontend product page.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
