# Attribute Values Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `attribute_values`  
**Engine**: InnoDB

---

## Overview

The `attribute_values` table stores the specific options available for a given `attribute`. For example, if the
Attribute is "Size", the Values might be "S", "M", "L".

**Related Tables**:

- `attributes`: Parent attribute.
- `attribute_category`: (Optional) Links attributes to specific categories.

---

## Column Specifications

### **Primary Key**

#### `id` - Value ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Value Details**

#### `attribute_id` - Parent Attribute

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `attributes.id`.

#### `value` - The Value

```sql
varchar(255) NOT NULL
```

- **Purpose**: The actual text value (e.g., "Small", "Cotton", "128GB").

#### `color_code` - Color Hex (Optional)

```sql
varchar(100) DEFAULT NULL
```

- **Purpose**: DEPRECATED/Legacy. Colors are now typically handled via the `colors` table, but some implementations
  might still use this for attribute-based colors.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```

---

## Relationships

### Belongs To Attribute

```php
public function attribute()
{
    return $this->belongsTo(Attribute::class);
}
```
