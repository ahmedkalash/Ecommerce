# Notifications Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `notifications`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `notifications` table implements the standard Laravel Database Notification system. It stores system-generated
alerts for users (e.g., "Order Shipped", "New Message Received").

---

## Column Breakdown

| Column                   | Type                  | Nullable | Key | Default | Extra | Description                                                                                     |
|:-------------------------|:----------------------|:---------|:----|:--------|:------|:------------------------------------------------------------------------------------------------|
| **id**                   | `char(36)`            | No       | PRI | *NULL*  |       | UUID string for the notification (Laravel standard).                                            |
| **notification_type_id** | `int(11)`             | No       |     | *NULL*  |       | Reference to the application-specific notification template/category (`notification_types.id`). |
| **type**                 | `varchar(191)`        | No       |     | *NULL*  |       | The fully qualified class name of the Laravel Notification class.                               |
| **notifiable_type**      | `varchar(191)`        | No       | MUL | *NULL*  |       | The model type receiving the notification (usually `App\Models\User`).                          |
| **notifiable_id**        | `bigint(20) unsigned` | No       |     | *NULL*  |       | The ID of the model receiving the notification.                                                 |
| **data**                 | `text`                | No       |     | *NULL*  |       | JSON-encoded payload containing notification content (message, links, etc.).                    |
| **read_at**              | `timestamp`           | Yes      |     | *NULL*  |       | Timestamp when the user viewed/read the notification. NULL if unread.                           |
| **created_at**           | `timestamp`           | Yes      |     | *NULL*  |       | Timestamp when the notification was generated.                                                  |
| **updated_at**           | `timestamp`           | Yes      |     | *NULL*  |       | Timestamp for the last record update.                                                           |

---

## Relationships

- **Notification Types**:
    - `notification_type_id` -> `notification_types.id`
- **Notifiable (Polymorphic)**:
    - Links to any "Notifiable" model (usually `User`).

---

## Common Queries

### Unread Notifications for a User

```sql
SELECT * FROM notifications 
WHERE notifiable_id = ? 
  AND notifiable_type = 'App\\Models\\User' 
  AND read_at IS NULL 
ORDER BY created_at DESC;
```

### Mark all as Read

```sql
UPDATE notifications 
SET read_at = NOW() 
WHERE notifiable_id = ? 
  AND read_at IS NULL;
```

---

## Notes & Observations

- **Custom Integration**: While following the standard Laravel schema, this table includes `notification_type_id`, which
  is a custom column for this CMS to link notifications to specific management settings.
- **Payload**: The `data` column is the most important for frontend display, as it contains the specific message and
  metadata for the alert.
