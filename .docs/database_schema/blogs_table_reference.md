# Blogs Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `blogs`  
**Engine**: InnoDB

---

## Overview

The `blogs` table stores blog posts or news articles published on the platform.

**Related Tables**:

- `blog_categories`: (Assumed, typically exists).

---

## Column Specifications

### **Primary Key**

#### `id` - Blog ID

```sql
bigint(20) unsigned NOT NULL auto_increment primary key
```

---

### **Content**

#### `category_id` - Category

```sql
int(11) NOT NULL
```

#### `title` - Headline

```sql
varchar(255) NOT NULL
```

#### `slug` - URL Slug

```sql
varchar(255) NOT NULL
```

#### `short_description` - Excerpt

```sql
text
DEFAULT NULL
```

#### `description` - Full Content

```sql
longtext
DEFAULT NULL
```

#### `banner` - Cover Image

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Reference to `uploads.id`.

---

### **SEO**

#### `meta_title`, `meta_img`, `meta_description`, `meta_keywords`

```sql
text
/ varchar
```

---

### **Status**

#### `status` - Published Status

```sql
int(1) NOT NULL DEFAULT 1
```

- **Values**: `1` (Published), `0` (Draft/Hidden).

---

### **Timestamps**

#### `created_at`, `updated_at`, `deleted_at`

```sql
timestamp
DEFAULT NULL
```

- **Note**: Supports soft deletes.
