# Ticket Replies Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `ticket_replies`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `ticket_replies` table stores the response history for a support ticket. It contains messages from both the user who
opened the ticket and the support staff/admin responding to it.

---

## Column Breakdown

| Column         | Type        | Nullable | Key | Default               | Extra                           | Description                                                              |
|:---------------|:------------|:---------|:----|:----------------------|:--------------------------------|:-------------------------------------------------------------------------|
| **id**         | `int(11)`   | No       | PRI | *NULL*                | `auto_increment`                | Internal unique identifier for the reply.                                |
| **ticket_id**  | `int(11)`   | No       |     | *NULL*                |                                 | Reference to the parent support ticket (`tickets.id`).                   |
| **user_id**    | `int(11)`   | No       |     | *NULL*                |                                 | Reference to the user who wrote the reply (`users.id`).                  |
| **reply**      | `longtext`  | No       |     | *NULL*                |                                 | The content of the response.                                             |
| **files**      | `longtext`  | Yes      |     | *NULL*                |                                 | JSON array of attachment references associated with this specific reply. |
| **created_at** | `timestamp` | No       |     | `current_timestamp()` | `on update current_timestamp()` | Timestamp when the reply was posted.                                     |
| **updated_at** | `timestamp` | No       |     | `current_timestamp()` |                                 | Timestamp for the last update to the reply record.                       |

---

## Relationships

- **Tickets**:
    - `ticket_id` -> `tickets.id`
- **Users**:
    - `user_id` -> `users.id` (Author of the reply)

---

## Common Queries

### Full Ticket History (Chronological)

```sql
SELECT r.*, u.name as author_name
FROM ticket_replies r
         JOIN users u ON r.user_id = u.id
WHERE r.ticket_id = ?
ORDER BY r.created_at ASC;
```

---

## Notes & Observations

- **Author Identification**: To determine if a reply is from "Support" or the "Client", the system checks the
  `user_type` of the linked `user_id`.
- **Primary Data**: Unlike some support systems that use the `messages` table for everything, this application uses
  dedicated tables for Tickets (`ticket_replies`) and Conversations (`messages`).
