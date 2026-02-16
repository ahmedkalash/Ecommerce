# Flash Deals Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `flash_deals`  
**Engine**: InnoDB

---

## Overview

The `flash_deals` table manages limited-time sales campaigns. Each flash deal contains a collection of products with
specific discount rates.

**Related Tables**:

- `flash_deal_products`: Pivot table linking products to the deal with custom prices.
- `flash_deal_translations`: Multilingual support.

---

## Column Specifications

### **Primary Key**

#### `id` - Deal ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Deal Information**

#### `title` - Campaign Title

```sql
varchar(255) DEFAULT NULL
```

- **Example**: "Black Friday Sale", "Summer Clearance".

#### `slug` - URL Slug

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: URL-friendly version of the title.

#### `banner` - Hero Image

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Upload ID for the campaign banner image.

---

### **Scheduling**

#### `start_date`, `end_date`

```sql
int(20) DEFAULT NULL
```

- **Purpose**: Unix timestamps for the campaign duration.
- **Note**: Using `int` for dates is a legacy pattern; typical Laravel apps use `timestamp`.

---

### **Status Flags**

#### `status` - Active State

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `0` (Inactive), `1` (Active).

#### `featured` - Homepage Feature

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `0` (Standard), `1` (Featured on homepage).

---

### **Appearance**

#### `background_color`, `text_color`

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Hex codes for styling the deal page.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
