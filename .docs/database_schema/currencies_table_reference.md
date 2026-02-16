# Currencies Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `currencies`  
**Engine**: InnoDB

---

## Overview

The `currencies` table defines supported currencies and their exchange rates relative to the base currency.

---

## Column Specifications

### **Primary Key**

#### `id` - Currency ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Details**

#### `name` - Currency Name

```sql
varchar(255) NOT NULL
```

- **Example**: `US Dollar`, `Euro`.

#### `code` - ISO Code

```sql
varchar(20) DEFAULT NULL
```

- **Example**: `USD`, `EUR`.

#### `symbol` - Display Symbol

```sql
varchar(255) NOT NULL
```

- **Example**: `$`, `€`.

---

### **Economics**

#### `exchange_rate` - Rate

```sql
double(10, 5) NOT NULL
```

- **Purpose**: Exchange rate relative to the system's default currency (1.00).

---

### **Status**

#### `status` - Active Status

```sql
int(10) NOT NULL DEFAULT 0
```

- **Values**: `1` (Active), `0` (Inactive).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
