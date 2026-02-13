# Seller Withdraw Requests Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `seller_withdraw_requests`  
**Engine**: InnoDB

---

## Overview

The `seller_withdraw_requests` table manages the payout process for sellers. Sellers request withdrawals from their
accumulated earnings (`seller_earning` in `users` table), and admins approve/reject them here.

**Related Tables**:

- `users`: The seller making the request.

---

## Column Specifications

### **Primary Key**

#### `id` - Request ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Request Details**

#### `user_id` - Seller

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `users.id`.

#### `amount` - Requested Amount

```sql
double(20, 2) DEFAULT NULL
```

- **Purpose**: The value to be withdrawn.
- **Constraint**: Must be <= `users.balance` (seller balance).

#### `message` - Notes

```sql
longtext
DEFAULT NULL
```

- **Purpose**: Optional message from seller to admin.

---

### **Status Tracking**

#### `status` - Approval State

```sql
int(1) DEFAULT 0
```

- **Values**:
    - `0`: Pending (New request).
    - `1`: Approved (Funds sent).
    - `2`: Rejected.

#### `viewed` - Admin Notification

```sql
int(1) DEFAULT 0
```

- **Values**: `0` (Unread), `1` (Read).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
