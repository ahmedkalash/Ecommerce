# Subscribers Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `subscribers`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `subscribers` table stores email addresses of users who have signed up for the newsletter, typically through a
footer or popup form on the frontend.

---

## Column Breakdown

| Column         | Type          | Nullable | Key | Default               | Extra            | Description                                                         |
|:---------------|:--------------|:---------|:----|:----------------------|:-----------------|:--------------------------------------------------------------------|
| **id**         | `int(11)`     | No       | PRI | *NULL*                | `auto_increment` | Internal unique identifier for the subscriber.                      |
| **email**      | `varchar(50)` | No       | UNI | *NULL*                |                  | The email address of the subscriber. Constrained by a unique index. |
| **created_at** | `timestamp`   | Yes      |     | `current_timestamp()` |                  | Timestamp when the user subscribed.                                 |
| **updated_at** | `timestamp`   | No       |     | `current_timestamp()` |                  | Timestamp for the last record update.                               |

---

## Relationships

- **Users**: There is no direct foreign key link, as non-registered guests can also subscribe. However, if a user
  registers with the same email, they may be reconciled by application logic.

---

## Common Queries

### List all Subscribers

```sql
SELECT email, created_at
FROM subscribers
ORDER BY created_at DESC;
```

---

## Notes & Observations

- **Email Length**: The `email` column is limited to `50` characters, which might be restrictive for long modern email
  addresses.
- **Verification**: This table does not include a `verified_at` column, implying subscriptions might be auto-approved or
  verification status is managed elsewhere.
- **Unsubscribe**: There is no `status` column. To unsubscribe, a record is likely deleted from the table.
