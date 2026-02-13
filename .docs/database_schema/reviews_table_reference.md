# Reviews Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `reviews`  
**Engine**: InnoDB

---

## Overview

The `reviews` table stores customer feedback and ratings for products. It is a critical component for social proof and
trust building.

**Related Tables**:

- `products`: The product being reviewed.
- `users`: The customer who wrote the review.

---

## Column Specifications

### **Primary Key**

#### `id` - Review ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **Review Data**

#### `product_id` - Product

```sql
int(11) NOT NULL
```

- **Purpose**: Foreign key to `products.id`.

#### `user_id` - Customer

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `users.id`.
- **Nullable**: Yes (but typically required for verified reviews).

#### `rating` - Star Rating

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Integer rating (1-5 stars).

#### `comment` - Review Text

```sql
mediumtext NOT NULL
```

- **Purpose**: Customer's written feedback.

#### `photos` - Review Images

```sql
varchar(191) DEFAULT NULL
```

- **Purpose**: Comma-separated list of upload IDs (customer photos of the product).

---

### **Display Settings**

#### `status` - Approval Status

```sql
int(1) NOT NULL DEFAULT 1
```

- **Values**:
    - `0`: Pending/Hidden.
    - `1`: Approved/Visible.

#### `viewed` - Admin Notification

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**:
    - `0`: New (unread by admin).
    - `1`: Viewed by admin.

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
