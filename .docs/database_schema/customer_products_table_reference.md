# Customer Products (Classifieds) Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `customer_products`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `customer_products` table manages the "Classified Ads" system, allowing regular customers to list their own used or
new items for sale. Unlike the primary `products` table (used by Sellers), these items generally do not use the standard
checkout process and are often focused on direct contact between buyer and seller.

---

## Column Breakdown

| Column                | Type           | Nullable | Key | Default               | Extra            | Description                                                             |
|:----------------------|:---------------|:---------|:----|:----------------------|:-----------------|:------------------------------------------------------------------------|
| **id**                | `int(11)`      | No       | PRI | *NULL*                | `auto_increment` | Internal unique identifier for the classified product.                  |
| **name**              | `varchar(255)` | Yes      |     | *NULL*                |                  | The display name/title of the item.                                     |
| **published**         | `int(1)`       | No       |     | `0`                   |                  | Flag for user-controlled visibility (`1` = visible).                    |
| **status**            | `int(1)`       | No       |     | `0`                   |                  | Admin-controlled approval status (`1` = approved).                      |
| **added_by**          | `varchar(50)`  | Yes      |     | *NULL*                |                  | Typically `customer` (distinguishes from `admin` or `seller` products). |
| **user_id**           | `int(11)`      | Yes      |     | *NULL*                |                  | Reference to the customer who listed the item (`users.id`).             |
| **category_id**       | `int(11)`      | Yes      |     | *NULL*                |                  | Primary category reference.                                             |
| **subcategory_id**    | `int(11)`      | Yes      |     | *NULL*                |                  | Sub-category reference.                                                 |
| **subsubcategory_id** | `int(11)`      | Yes      |     | *NULL*                |                  | Level 3 category reference.                                             |
| **brand_id**          | `int(11)`      | Yes      |     | *NULL*                |                  | Brand reference.                                                        |
| **photos**            | `varchar(255)` | Yes      |     | *NULL*                |                  | Comma-separated list or JSON of image references.                       |
| **thumbnail_img**     | `varchar(150)` | Yes      |     | *NULL*                |                  | Main thumbnail image path.                                              |
| **conditon**          | `varchar(50)`  | Yes      |     | *NULL*                |                  | The condition of the item (e.g., `new`, `used`).                        |
| **location**          | `text`         | Yes      |     | *NULL*                |                  | Specific location or area where the item is available.                  |
| **unit_price**        | `double(20,2)` | Yes      |     | `0.00`                |                  | The asking price for the item.                                          |
| **description**       | `mediumtext`   | Yes      |     | *NULL*                |                  | Detailed description of the item.                                       |
| **slug**              | `varchar(200)` | Yes      |     | *NULL*                |                  | URL-friendly identifier.                                                |
| **created_at**        | `timestamp`    | No       |     | `current_timestamp()` |                  | Timestamp of listing creation.                                          |
| **updated_at**        | `timestamp`    | No       |     | `current_timestamp()` |                  | Timestamp of last modification.                                         |

---

## Relationships

- **Users**:
    - `user_id` -> `users.id`
- **Categories**:
    - `category_id` -> `categories.id`
- **Translations**:
    - Links to `customer_product_translations`.

---

## Common Queries

### Retrieve Approved Classified Ads

```sql
SELECT *
FROM customer_products
WHERE status = 1
  AND published = 1
ORDER BY created_at DESC;
```

### List Ads by Category

```sql
SELECT *
FROM customer_products
WHERE category_id = ?
  AND status = 1
ORDER BY unit_price ASC;
```

---

## Notes & Observations

- **Typo Check**: The column `conditon` (missing 'i') is a known typo in the schema.
- **Workflow**: Items listed by customers usually require admin approval (`status`) before appearing publically on the "
  Classifieds" section.
- **Media**: Like the main product table, `photos` and `thumbnail_img` store strings or IDs that link to files.
