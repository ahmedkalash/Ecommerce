# Conversations Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `conversations`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `conversations` table acts as a header for internal messaging threads between users (typically between a Customer
and a Seller). Individual messages are stored in the `messages` table and linked via `conversation_id`.

---

## Column Breakdown

| Column              | Type            | Nullable | Key | Default               | Extra            | Description                                                                            |
|:--------------------|:----------------|:---------|:----|:----------------------|:-----------------|:---------------------------------------------------------------------------------------|
| **id**              | `int(11)`       | No       | PRI | *NULL*                | `auto_increment` | Internal unique identifier for the conversation thread.                                |
| **sender_id**       | `int(11)`       | No       |     | *NULL*                |                  | Reference to the user who initiated the conversation (`users.id`).                     |
| **receiver_id**     | `int(11)`       | No       |     | *NULL*                |                  | Reference to the user who is the target of the conversation (`users.id`).              |
| **title**           | `varchar(1000)` | Yes      |     | *NULL*                |                  | The subject or title of the conversation.                                              |
| **sender_viewed**   | `int(1)`        | No       |     | `1`                   |                  | Boolean-style flag (`1`/`0`) indicating if the sender has viewed the latest reply.     |
| **receiver_viewed** | `int(1)`        | No       |     | `0`                   |                  | Boolean-style flag (`1`/`0`) indicating if the receiver has viewed the latest message. |
| **created_at**      | `timestamp`     | No       |     | `current_timestamp()` |                  | Timestamp when the conversation thread was started.                                    |
| **updated_at**      | `timestamp`     | No       |     | `current_timestamp()` |                  | Timestamp when the conversation thread was last updated (e.g., new message).           |

---

## Relationships

- **Users**:
    - `sender_id` -> `users.id`
    - `receiver_id` -> `users.id`
- **Messages**:
    - One-to-Many relationship with the `messages` table (`messages.conversation_id`).

---

## Common Queries

### Active Conversations for a Specific User

```sql
SELECT *
FROM conversations
WHERE sender_id = ?
   OR receiver_id = ?
ORDER BY updated_at DESC;
```

### Unread Messages for a User

```sql
-- If user is the receiver
SELECT COUNT(*)
FROM conversations
WHERE receiver_id = ?
  AND receiver_viewed = 0;

-- If user is the sender
SELECT COUNT(*)
FROM conversations
WHERE sender_id = ?
  AND sender_viewed = 0;
```

---

## Notes & Observations

- **Soft Deletes**: Not present. Conversations are typically permanent until deleted by both parties (though no
  `deleted_by_...` flags are present here).
- **Messaging Logic**: Usually involves Customers contacting Sellers. In some configurations, it could be used for
  Admin-User communication, though `tickets` is preferred for support.
- **Title Length**: The `title` column is quite large (`1000`), allowing for descriptive subjects.
