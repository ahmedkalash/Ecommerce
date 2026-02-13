# Categories Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `categories`  
**Engine**: InnoDB

---

## Overview

The `categories` table defines the hierarchical structure for product classification. It supports unlimited nesting
levels, though typical usage involves 2-3 levels (Category -> Sub-Category -> Sub-Sub-Category).

**Related Tables**:

- `products`: Linked via `category_id`.
- `categories`: Self-referencing (`parent_id`) for hierarchy.

---

## Column Specifications

### **Primary Key**

#### `id` - Category ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Hierarchy**

#### `parent_id` - Parent Category

```sql
int(11) DEFAULT 0
```

- **Purpose**: ID of the parent category. `0` indicates a root-level category.

#### `level` - Depth Level

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Optimization for hierarchy traversal (0=Root, 1=Sub, 2=Sub-Sub).

#### `order_level` - Sort Order

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Display order within the same level.

---

### **Details**

#### `name` - Category Name

```sql
varchar(50) NOT NULL
```

#### `slug` - URL Slug

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: SEO-friendly URL segment.

#### `banner`, `icon`, `cover_image`

```sql
varchar(100) DEFAULT NULL
```

- **Purpose**: References to image paths or upload IDs.

---

### **Financials & Settings**

#### `commision_rate` - Seller Commission

```sql
double(8, 2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Percentage of sale taken by admin for products in this category.

#### `discount` - Global Discount

```sql
double(20, 2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Category-wide discount percentage/amount.

#### `digital` - Digital Products?

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `1` (Digital), `0` (Physical).

---

### **Status & Visibility**

#### `featured` - Featured?

```sql
int(1) NOT NULL DEFAULT 0
```

#### `top` - Top Category?

```sql
int(1) NOT NULL DEFAULT 0
```

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
DEFAULT NULL
```
