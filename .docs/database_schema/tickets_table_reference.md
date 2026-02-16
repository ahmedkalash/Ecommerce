# Tickets Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `tickets`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `tickets` table manages support requests initiated by users (typically Customers or Sellers) directed to the System
Administrator. It tracks the overall status of the support request and initial details.

---

## Column Breakdown

| Column            | Type           | Nullable | Key | Default               | Extra                           | Description                                                                                              |
|:------------------|:---------------|:---------|:----|:----------------------|:--------------------------------|:---------------------------------------------------------------------------------------------------------|
| **id**            | `int(11)`      | No       | PRI | *NULL*                | `auto_increment`                | Internal unique identifier for the support ticket.                                                       |
| **code**          | `bigint(23)`   | No       |     | *NULL*                |                                 | A unique public-facing numeric code for tracking the ticket (e.g., ticket number).                       |
| **user_id**       | `int(11)`      | No       |     | *NULL*                |                                 | Reference to the user who opened the ticket (`users.id`).                                                |
| **subject**       | `varchar(255)` | No       |     | *NULL*                |                                 | The title or subject of the support request.                                                             |
| **details**       | `longtext`     | Yes      |     | *NULL*                |                                 | The initial detailed description of the issue provided by the user.                                      |
| **files**         | `longtext`     | Yes      |     | *NULL*                |                                 | JSON array or comma-separated list of IDs referencing uploaded attachments (`uploads.id` or `media.id`). |
| **status**        | `varchar(10)`  | No       |     | `pending`             |                                 | Current status of the ticket (e.g., `pending`, `open`, `closed`).                                        |
| **viewed**        | `int(1)`       | No       |     | `0`                   |                                 | Flag indicating if the Admin has viewed the ticket or the latest reply from the user.                    |
| **client_viewed** | `int(1)`       | No       |     | `0`                   |                                 | Flag indicating if the User (client) has viewed the latest reply from the admin.                         |
| **created_at**    | `timestamp`    | No       |     | `current_timestamp()` | `on update current_timestamp()` | Timestamp when the ticket was created.                                                                   |
| **updated_at**    | `timestamp`    | No       |     | `current_timestamp()` |                                 | Timestamp when the ticket was last updated.                                                              |

---

## Relationships

- **Users**:
    - `user_id` -> `users.id`
- **Ticket Replies**:
    - One-to-Many relationship with the `ticket_replies` table.

---

## Common Queries

### Open Tickets for Admin

```sql
SELECT *
FROM tickets
WHERE status != 'closed'
ORDER BY created_at DESC;
```

### Tickets Opened by a Specific User

```sql
SELECT *
FROM tickets
WHERE user_id = ?
ORDER BY created_at DESC;
```

---

## Notes & Observations

- **Ticket Coding**: The `code` column is often generated as a random large integer or a timestamp-based sequence to
  provide a non-sequential reference.
- **View States**: Similar to conversations, view state is tracked via simple flags (`viewed`, `client_viewed`).
- **File Management**: The `files` column suggests a legacy attachment system (JSON of IDs) but modern parts of the CMS
  might use Spatie Media Library.
