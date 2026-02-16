# Transactions Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `transactions`  
**Engine**: InnoDB

---

## Overview

The `transactions` table logs general financial movements or specific payment events that don't fit into `orders` or
`wallets`. It's often used for MPESA transactions or custom payment flows.

**Related Tables**:

- `users`: User involved in the transaction.

---

## Column Specifications

### **Primary Key**

#### `id` - ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Transaction Details**

#### `user_id` - User

```sql
int(11) NOT NULL
```

#### `gateway` - Payment Gateway

```sql
varchar(255) DEFAULT NULL
```

#### `payment_type` - Type

```sql
varchar(255) DEFAULT NULL
```

- **Example**: `mpesa_payment`, `wallet_recharge`.

#### `additional_content` - Metadata

```sql
text
DEFAULT NULL
```

- **Purpose**: Contextual data about the transaction.

---

### **MPESA Specifics**

#### `mpesa_request`, `mpesa_receipt`

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Specific fields for MPESA integration tracking.

---

### **Status**

#### `status` - Payment Status

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**: `0` (Failed/Pending), `1` (Success).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp
NOT NULL DEFAULT current_timestamp()
```
