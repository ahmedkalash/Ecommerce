# Messages Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `messages`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `messages` table stores individual messages sent within a conversation thread. It links to the `conversations` table
and tracks which user sent the message.

---

## Column Breakdown

| Column              | Type        | Nullable | Key | Default               | Extra            | Description                                                        |
|:--------------------|:------------|:---------|:----|:----------------------|:-----------------|:-------------------------------------------------------------------|
| **id**              | `int(11)`   | No       | PRI | *NULL*                | `auto_increment` | Internal unique identifier for the message.                        |
| **conversation_id** | `int(11)`   | No       |     | *NULL*                |                  | Reference to the parent conversation thread (`conversations.id`).  |
| **user_id**         | `int(11)`   | No       |     | *NULL*                |                  | Reference to the user who sent this specific message (`users.id`). |
| **message**         | `text`      | Yes      |     | *NULL*                |                  | The content of the message.                                        |
| **created_at**      | `timestamp` | No       |     | `current_timestamp()` |                  | Timestamp when the message was sent.                               |
| **updated_at**      | `timestamp` | No       |     | `current_timestamp()` |                  | Timestamp for the last update to the message record.               |

---

## Relationships

- **Conversations**:
    - `conversation_id` -> `conversations.id`
- **Users**:
    - `user_id` -> `users.id` (The author of the message)

---

## Common Queries

### Retrieve Messages for a Conversation

```sql
SELECT m.*, u.name as sender_name 
FROM messages m
JOIN users u ON m.user_id = u.id
WHERE m.conversation_id = ? 
ORDER BY m.created_at ASC;
```

### Last Message in a Conversation

```sql
SELECT message FROM messages 
WHERE conversation_id = ? 
ORDER BY created_at DESC 
LIMIT 1;
```

---

## Notes & Observations

- **Attachments**: This table does not have a `file` or `attachment` column. File uploads in conversations might be
  handled via specific HTML in the text or managed by the Spatie Media Library (check `media` table).
- **Read Status**: Individual message read status is not tracked. Instead, read status is managed at the conversation
  level via `conversations.sender_viewed` and `conversations.receiver_viewed`.
