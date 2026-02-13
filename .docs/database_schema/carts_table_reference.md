# Carts Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `carts`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `carts` table handles persistent shopping cart data for both guests and registered users. It stores item selections,
variations, and calculated pricing metadata required for the checkout process.

---

## Column Breakdown

| Column             | Type               | Nullable | Key | Default               | Extra            | Description                                                    |
|:-------------------|:-------------------|:---------|:----|:----------------------|:-----------------|:---------------------------------------------------------------|
| **id**             | `int(11) unsigned` | No       | PRI | *NULL*                | `auto_increment` | Unique identifier for the cart item.                           |
| **user_id**        | `int(11)`          | Yes      |     | *NULL*                |                  | Reference to the registered user (`users.id`).                 |
| **temp_user_id**   | `varchar(255)`     | Yes      |     | *NULL*                |                  | Session-based ID for guest users.                              |
| **owner_id**       | `int(11)`          | Yes      |     | *NULL*                |                  | Reference to the owner of the product (Seller/Admin user ID).  |
| **product_id**     | `int(11)`          | Yes      |     | *NULL*                |                  | Reference to the product being purchased.                      |
| **variation**      | `text`             | Yes      |     | *NULL*                |                  | String representing the selected attributes (e.g., `Blue-XL`). |
| **price**          | `double(20,2)`     | Yes      |     | `0.00`                |                  | Unit price of the item at the time of adding to cart.          |
| **tax**            | `double(20,2)`     | Yes      |     | `0.00`                |                  | Calculated tax for this item.                                  |
| **shipping_cost**  | `double(20,2)`     | No       |     | `0.00`                |                  | Calculated shipping fee for this item.                         |
| **shipping_type**  | `varchar(30)`      | No       |     | `""`                  |                  | Method of shipping (e.g., `flat_rate`, `carrier`).             |
| **pickup_point**   | `int(11)`          | Yes      |     | *NULL*                |                  | Reference to a local pickup location ID.                       |
| **carrier_id**     | `int(11)`          | Yes      |     | *NULL*                |                  | Reference to the shipping carrier ID.                          |
| **quantity**       | `int(11)`          | No       |     | `0`                   |                  | Number of units selected.                                      |
| **discount**       | `double(10,2)`     | No       |     | `0.00`                |                  | Any specific product-level discount applied.                   |
| **coupon_code**    | `varchar(255)`     | Yes      |     | *NULL*                |                  | Coupon code applied to this specific item/cart.                |
| **coupon_applied** | `tinyint(4)`       | No       |     | `0`                   |                  | Boolean flag indicating if a coupon is active.                 |
| **created_at**     | `timestamp`        | Yes      |     | `current_timestamp()` |                  | Timestamp when the item was added.                             |
| **updated_at**     | `timestamp`        | Yes      |     | `current_timestamp()` |                  | Timestamp of last update.                                      |

---

## Relationships

- **Users**:
    - `user_id` -> `users.id`
- **Products**:
    - `product_id` -> `products.id`
- **Shops**:
    - `owner_id` (User ID of Seller) links to `shops`.

---

## Common Queries

### Retrieve Cart items for a User

```sql
SELECT *
FROM carts
WHERE user_id = ?
   OR (temp_user_id = ? AND user_id IS NULL);
```

---

## Notes & Observations

- **Persistent Guest Carts**: The `temp_user_id` allows users to start shopping without an account. When they eventually
  log in, the system typically merges the `temp_user_id` record into their permanent `user_id`.
- **Draft Orders**: Carts are essentially draft orders. Once checkout is complete, records are usually converted/copied
  to `orders` and `order_details` tables, and then cleared from `carts`.
- **Pricing**: Storing `price` and `tax` directly in the cart ensures that the price doesn't suddenly change in the
  middle of a checkout session if the seller updates the product price.
