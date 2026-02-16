# Payments Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `payments`  
**Engine**: InnoDB

---

## Overview

The `payments` table records the outcomes of various payment attempts. It serves as a unified log for payments made via
gateways (Stripe, PayPal, etc.) or manually.

**Related Tables**:

- `orders`: Linked via `payment_details` JSON or external references.
- `seller_packages`: Payments for subscription packages.

---

## Column Specifications

### **Primary Key**

#### `id` - Payment ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Payment Info**

#### `seller_id` - Payee

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `users.id` (user_type='seller').
- **Note**: The column name implies 'seller', but it can also be used for customer payments depending on context.

#### `amount` - Amount Paid

```sql
double(20,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: The transaction amount.

#### `payment_method` - Gateway

```sql
varchar(255) DEFAULT NULL
```

- **Examples**: `stripe`, `paypal`, `cash_on_delivery`, `wallet`.

#### `txn_code` - Transaction ID

```sql
varchar(100) DEFAULT NULL
```

- **Purpose**: External reference ID provided by the payment gateway.

#### `payment_details` - Metadata

```sql
longtext DEFAULT NULL
```

- **Purpose**: JSON string containing gateway response data.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
