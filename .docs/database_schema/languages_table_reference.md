# Languages Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `languages`  
**Engine**: InnoDB

---

## Overview

The `languages` table defines the available languages for the platform.

**Related Tables**:

- `translations`: Stores the actual translated strings.

---

## Column Specifications

### **Primary Key**

#### `id` - Language ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Details**

#### `name` - Display Name

```sql
varchar(255) NOT NULL
```

- **Example**: `English`, `Arabic`.

#### `code` - Language Code

```sql
varchar(20) DEFAULT NULL
```

- **Example**: `en`, `ar`, `fr`.

#### `app_lang_code` - App Reference

```sql
varchar(255) DEFAULT 'en'
```

- **Purpose**: Code used for mobile app or specific integrations.

#### `rtl` - Direction

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `1` (Right-to-Left), `0` (Left-to-Right).

---

### **Status**

#### `status` - Active Status

```sql
int(10) NOT NULL DEFAULT 0
```

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
