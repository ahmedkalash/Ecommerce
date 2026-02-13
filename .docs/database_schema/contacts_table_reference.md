# Contacts Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `contacts`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `contacts` table stores submissions from the "Contact Us" form on the website. This is separate from the Support
Ticket system and is usually used by non-registered guests or for pre-sales inquiries.

---

## Column Breakdown

| Column         | Type               | Nullable | Key | Default               | Extra                           | Description                                                     |
|:---------------|:-------------------|:---------|:----|:----------------------|:--------------------------------|:----------------------------------------------------------------|
| **id**         | `int(11) unsigned` | No       | PRI | *NULL*                | `auto_increment`                | Internal unique identifier for the inquiry.                     |
| **name**       | `varchar(255)`     | No       |     | *NULL*                |                                 | Name of the person who sent the inquiry.                        |
| **email**      | `varchar(191)`     | No       |     | *NULL*                |                                 | Email address for follow-up.                                    |
| **phone**      | `varchar(20)`      | Yes      |     | *NULL*                |                                 | Optional phone number of the sender.                            |
| **content**    | `text`             | No       |     | *NULL*                |                                 | The actual message/inquiry text.                                |
| **image**      | `varchar(191)`     | Yes      |     | *NULL*                |                                 | Path or reference to an uploaded image/attachment (if allowed). |
| **reply**      | `text`             | Yes      |     | *NULL*                |                                 | The response sent by the Admin (stored after replying).         |
| **created_at** | `timestamp`        | No       |     | `current_timestamp()` |                                 | Timestamp when the inquiry was received.                        |
| **updated_at** | `timestamp`        | No       |     | `current_timestamp()` | `on update current_timestamp()` | Timestamp for the last record update.                           |

---

## Relationships

- **Independent**: This table typically does not link to `users` because it is designed for guest access, though it
  stores the email for identification.

---

## Common Queries

### Unanswered Contact Inquiries

```sql
SELECT *
FROM contacts
WHERE reply IS NULL
ORDER BY created_at DESC;
```

---

## Notes & Observations

- **Simple Workflow**: Contact inquiries are often simpler than support tickets. The `reply` is stored directly in the
  record rather than using a separate relationship.
- **Data Cleanup**: Since this table can grow with spam, it's often a candidate for periodic pruning.
