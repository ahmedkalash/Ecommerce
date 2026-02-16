# Brands Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `brands`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `brands` table stores product brand information. Products can optionally be linked to a brand, which allows
customers to filter and shop by their favorite manufacturers.

**Related Tables**:

- `products`: Linked via `brand_id`.
- `uploads`: Stores the brand logo image.

---

## Column Specifications

### **Primary Key**

#### `id` - Brand ID

```sql
int(11) NOT NULL auto_increment primary key
```

- **Purpose**: Unique identifier for the brand.
- **Usage**: Referenced in `products` table.

---

### **Brand Information**

#### `name` - Brand Name

```sql
varchar(50) NOT NULL
```

- **Purpose**: Display name of the brand.
- **Example**: "Samsung", "Nike", "Adidas".

#### `logo` - Brand Logo

```sql
varchar(100) DEFAULT NULL
```

- **Purpose**: Upload ID or relative path to the brand's logo image.
- **Usage**: Displayed on brand pages and product details.

#### `slug` - SEO Slug

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: URL-friendly version of the brand name.
- **Usage**: `example.com/brand/samsung`

---

### **Display Settings**

#### `top` - Top Brand Flag

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**:
    - `0`: Regular brand.
    - `1`: Top brand (featured in specific homepage sections).

---

### **SEO Metadata**

#### `meta_title` - SEO Title

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: HTML title tag for the brand page.

#### `meta_description` - SEO Description

```sql
text DEFAULT NULL
```

- **Purpose**: Meta description for search engines.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
created_at TIMESTAMP NOT NULL DEFAULT current_timestamp()
updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp()
```

---

## Relationships

### Has Many Products

```php
public function products()
{
    return $this->hasMany(Product::class);
}
```
