# Zones Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `zones`  
**Engine**: InnoDB

---

## Overview

The `zones` table defines tax zones for the system. Note that "Shipping Zones" might be handled differently (see
`countries.zone_id`), but this table often relates to tax jurisdiction grouping.

---

## Column Specifications

### **Primary Key**

#### `id` - Zone ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Details**

#### `name` - Zone Name

```sql
varchar(255) NOT NULL
```

- **Example**: `East Coast`, `EU`, `Local`.

---

### **Status**

#### `status` - Active

```sql
tinyint(1) NOT NULL
```

- **Values**: `0` (Inactive), `1` (Active).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
