# Wallets Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `wallets`  
**Engine**: InnoDB

---

## Overview

The `wallets` table tracks **recharge history** for customer wallets. It does *not* store the current balance. The
current balance is typically stored in the `users.balance` column.

**Key Concept**: This table acts as a ledger for "money in" (deposits/recharges). "Money out" (payments) is tracked in
`orders` or `payments`.

**Related Tables**:

- `users`: The wallet owner.

---

## Column Specifications

### **Primary Key**

#### `id` - Transaction ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Recharge Details**

#### `user_id` - User

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `users.id`.

#### `amount` - Recharged Amount

```sql
double(20, 2) NOT NULL
```

- **Purpose**: Amount added to the wallet.

#### `payment_method` - Source

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Gateway used for the recharge (e.g., `paypal`, `stripe`).

#### `payment_details` - Metadata

```sql
longtext
DEFAULT NULL
```

- **Purpose**: JSON string containing gateway response.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```

---

## Business Logic

- When a record is created here with successful payment, the `users.balance` column for the `user_id` is incremented.
