# Attributes Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `attributes`  
**Engine**: InnoDB

---

## Overview

The `attributes` table defines the Types of variations available for products (e.g., "Size", "Fabric", "Storage
Capacity"). It is the parent table for `attribute_values`.

**Related Tables**:

- `attribute_values`: Specific values for each attribute.
- `products`: referenced in `choice_options` JSON column.

---

## Column Specifications

### **Primary Key**

#### `id` - Attribute ID

```sql
int(11) NOT NULL auto_increment primary key
```

- **Purpose**: Unique identifier.

---

### **Attribute Details**

#### `name` - Attribute Name

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Name of the attribute (e.g., "Size", "Material").
- **Usage**: Label shown to customers when selecting variants.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```

---

## Relationships

### Has Many Values

```php
public function attribute_values()
{
    return $this->hasMany(AttributeValue::class);
}
```
