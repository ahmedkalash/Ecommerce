# Wishlists Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `wishlists`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `wishlists` table stores products that users have "saved for later" or marked as favorites.

---

## Column Breakdown

| Column         | Type        | Nullable | Key | Default               | Extra            | Description                                               |
|:---------------|:------------|:---------|:----|:----------------------|:-----------------|:----------------------------------------------------------|
| **id**         | `int(11)`   | No       | PRI | *NULL*                | `auto_increment` | Internal unique identifier for the wishlist entry.        |
| **user_id**    | `int(11)`   | No       |     | *NULL*                |                  | Reference to the user who saved the product (`users.id`). |
| **product_id** | `int(11)`   | No       |     | *NULL*                |                  | Reference to the saved product (`products.id`).           |
| **created_at** | `timestamp` | Yes      |     | `current_timestamp()` |                  | Timestamp when the product was added to the wishlist.     |
| **updated_at** | `timestamp` | No       |     | `current_timestamp()` |                  | Timestamp for the last record update.                     |

---

## Relationships

- **Users**:
    - `user_id` -> `users.id`
- **Products**:
    - `product_id` -> `products.id`

---

## Common Queries

### Retrieve a User's Wishlist

```sql
SELECT w.*, p.name, p.unit_price, p.thumbnail_img
FROM wishlists w
         JOIN products p ON w.product_id = p.id
WHERE w.user_id = ?;
```

---

## Notes & Observations

- **Uniqueness**: Application logic should ensure a user cannot add the same product multiple times to their wishlist.
- **Guest Access**: This table requires a `user_id`, so guests typically cannot move items to a persistent wishlist
  until they log in (though session-based favorites might exist separately).
