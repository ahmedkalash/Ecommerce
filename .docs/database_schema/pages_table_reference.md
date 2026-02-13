# Pages Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `pages`  
**Engine**: InnoDB

---

## Overview

The `pages` table stores content for static/custom pages created in the CMS (e.g., "About Us", "Privacy Policy", "
Terms & Conditions").

---

## Column Specifications

### **Primary Key**

#### `id` - Page ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Page Content**

#### `type` - Page Identifier

```sql
varchar(50) NOT NULL
```

- **Examples**: `home_page`, `about_us`, `mobile_home`.
- **Purpose**: System identifier for special pages.

#### `title` - Page Title

```sql
varchar(255) DEFAULT NULL
```

#### `slug` - URL Slug

```sql
varchar(255) DEFAULT NULL
```

#### `content` - HTML Body

```sql
longtext DEFAULT NULL
```

- **Purpose**: The main HTML content of the page.

---

### **SEO Metadata**

#### `meta_title`, `meta_description`, `keywords`

```sql
text / varchar
```

- **Purpose**: SEO tags.

#### `meta_image`

```sql
varchar(255) DEFAULT NULL
```

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
