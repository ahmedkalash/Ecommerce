# Business Settings Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `business_settings`  
**Engine**: InnoDB

---

## Overview

The `business_settings` table acts as a global key-value store for system configurations. It avoids the need for
hardcoded config files for dynamic settings manageable via the Admin Panel.

---

## Column Specifications

### **Primary Key**

#### `id` - ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Key-Value Pair**

#### `type` - Setting Key

```sql
varchar(255) NOT NULL
```

- **Examples**: `system_name`, `mail_config`, `paypal_payment`, `shipping_type`.

#### `value` - Setting Value

```sql
longtext
DEFAULT NULL
```

- **Format**: Can be plain string, boolean (1/0), or JSON string (for complex configs like mail or payment gateways).

#### `lang` - Language (Optional)

```sql
varchar(30) DEFAULT NULL
```

- **Purpose**: Supports localized values for settings if needed.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
DEFAULT NULL
```

---

## Usage Example

```php
// Helper function often used
$config = \App\Models\BusinessSetting::where('type', 'paypal_payment')->first()->value;
```
