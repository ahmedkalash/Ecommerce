# Categories Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `categories`  
**Engine**: InnoDB

---

## Overview

The `categories` table defines the hierarchical structure for product classification. It supports unlimited nesting levels, using an adjacency list model.

**Related Tables**:

- `product_categories`: Pivot table linking `products` to `categories` (Many-to-Many).
- `category_translations`: Stores multi-language names for categories.
- `media`: Visual assets (banners, icons, covers) are managed via Spatie Media Library and stored in the `media` table.

---

## Column Specifications

### **Primary Key**

#### `id` - Category ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Hierarchy (Adjacency List)**

#### `parent_id` - Parent Category

```sql
int(11) DEFAULT NULL
```

- **Purpose**: ID of the parent category. 
- **Constraint**: `FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL`.
- **Root Nodes**: Store `NULL`. (Refactored from `0` to support `staudenmeir/laravel-adjacency-list` and strict database integrity).

#### `level` - Depth Level

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Metadata for hierarchy traversal (0=Root, 1=Sub, 2=Sub-Sub).

#### `order_level` - Sort Order

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Manual display order within the same parent level.

---

### **Core Attributes**

#### `name` - Category Name

```sql
varchar(50) NOT NULL
```
- **Note**: Serves as the fallback name. Translatable names live in `category_translations`.

#### `slug` - URL Slug

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Unique, SEO-friendly URL segment.
- **Index**: `UNIQUE INDEX (slug)`.

---

### **Flags & Type**

#### `featured` - Featured Flag

```sql
int(1) NOT NULL DEFAULT 0
```
- **Purpose**: If `1`, category is displayed in homepage "Featured" sections.

#### `top` - Top Category Flag

```sql
int(1) NOT NULL DEFAULT 0
```
- **Purpose**: If `1`, category is highlighted in specific menu layouts.

#### `digital` - Digital Products Flag

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `1` (Allows digital/downloadable products), `0` (Physical).

---

### **Financials & Settings**

#### `commision_rate` - Admin Commission

```sql
double(8, 2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Percentage platform fee taken from sales of products in this category.

#### `refund_request_time` - Refund Window

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Number of days a customer has to request a refund for items in this category.

---

### **SEO Metadata**

#### `meta_title`

```sql
varchar(255) DEFAULT NULL
```

#### `meta_description`

```sql
text DEFAULT NULL
```

---

### **Media Assets**

Stored as references to the `media` table (Spatie Media Library).

- **`banner`**: Large image for category pages.
- **`icon`**: Small image for menus/navigation.
- **`cover_image`**: Image for category selection grids.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp DEFAULT CURRENT_TIMESTAMP
```

---

## Dropped Columns (Legacy)

The following columns were removed in the V10 Refactor to improve flexibility:
- `discount`: Category-level discounts are now managed at the Product or Flash Deal level.
- `discount_start_date` / `discount_end_date`: Removed.
- `products.category_id`: Removed (migrated to `product_categories` pivot table).
