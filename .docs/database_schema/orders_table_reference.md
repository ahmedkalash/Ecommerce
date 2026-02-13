# Orders Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `orders`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `orders` table represents **individual seller orders** within the multi-vendor e-commerce platform. When a customer
places an order, it is split into multiple `orders` records (one per seller) and grouped under a `combined_order` (the
customer's shopping cart checkout).

**Architecture**:

- **Customer places 1 checkout** → Creates 1 `combined_orders` record
- **Order contains products from 3 sellers** → Creates 3 `orders` records
- **Each seller order has line items** → Multiple `order_details` records

**Key Concept**: This table represents the **seller's view of the order**, not the customer's full checkout.

**Related Tables**:

- `combined_orders`: Groups all seller orders from one customer checkout
- `order_details`: Individual line items (products) in this seller order
- `users`: Customer who placed the order
- `sellers`: Seller fulfilling the order
- `payments`: Payment records
- `commissions`: Platform commission for this order

---

## Column Specifications

### **Primary Key**

#### `id` - Order ID

```sql
int(11) NOT NULL auto_increment primary key
```

- **Purpose**: Unique identifier for this seller order
- **Usage**: Referenced in `order_details`, `payments`, `refunds`, etc.

---

### **Order Relationships**

#### `combined_order_id` - Grouped Checkout ID

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `combined_orders.id`
- **Usage**: Links this seller order back to the customer's full checkout

**Example Scenario**:

```
Customer orders:
- Product A from Seller 1 ($50)
- Product B from Seller 2 ($30)
- Product C from Seller 1 ($20)

Result:
combined_order_id = 100 (total $100)
  ├─ Order 1: Seller 1, Products A+C ($70)
  └─ Order 2: Seller 2, Product B ($30)
```

**⚠️ Missing Constraint**: No foreign key to `combined_orders` table

---

#### `user_id` - Customer ID

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `users.id` (the customer who placed the order)
- **Nullable**: Yes (for guest checkout)

**Usage**:

```php
$order = Order::find(1);
$order->user->name; // Customer name
$order->user->email; // Customer email
```

---

#### `guest_id` - Guest Customer ID

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `guests` table for non-registered customers
- **Nullable**: Yes (NULL if registered user)

**Business Logic**:

```php
if ($order->user_id) {
    // Registered customer
    $customer = $order->user;
} else {
    // Guest checkout
    $guest = Guest::find($order->guest_id);
}
```

---

#### `seller_id` - Fulfilling Seller

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `users.id` where `user_type = 'seller'`
- **Usage**: Identifies which seller is responsible for fulfilling this order

**Seller Dashboard**:

```php
// Get seller's orders
$sellerOrders = Order::where('seller_id', auth()->id())->get();
```

---

### **Shipping Information**

#### `shipping_address` - Delivery Address

```sql
longtext DEFAULT NULL
```

- **Purpose**: JSON-encoded shipping address
- **Format**:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "address": "123 Main St",
  "city": "New York",
  "state": "NY",
  "postal_code": "10001",
  "country": "USA"
}
```

**Usage**:

```php
$shippingAddress = json_decode($order->shipping_address);
echo $shippingAddress->address;
```

**⚠️ Issue**: Address data duplicated across orders. Should reference `addresses` table.

---

#### `additional_info` - Extra Delivery Instructions

```sql
longtext DEFAULT NULL
```

- **Purpose**: Customer notes for delivery
- **Examples**:
    - "Leave package at front door"
    - "Call before delivery"
    - "Gift wrapping requested"

---

#### `shipping_type` - Shipping Method

```sql
varchar(50) NOT NULL
```

- **Values**:
    - `'free'`: Free shipping
    - `'flat_rate'`: Fixed rate shipping
    - `'carrier'`: Carrier-based shipping
    - `'pickup_point'`: Customer pickup

---

#### `pickup_point_id` - Pickup Location

```sql
int(11) NOT NULL DEFAULT 0
```

- **Purpose**: Foreign key to `pickup_points.id`
- **Usage**: If `shipping_type = 'pickup_point'`
- **Default**: 0 (no pickup point)

---

#### `carrier_id` - Shipping Carrier

```sql
int(11) DEFAULT NULL
```

- **Purpose**: Foreign key to `carriers.id`
- **Usage**: If `shipping_type = 'carrier'`
- **Examples**: FedEx, UPS, DHL

---

### **Order Source**

#### `order_from` - Order Channel

```sql
varchar(20) NOT NULL DEFAULT 'web'
```

- **Purpose**: Tracks where the order originated
- **Values**:
    - `'web'`: Desktop/mobile website
    - `'app'`: Mobile application
    - `'pos'`: Point of sale (in-store)
    - `'api'`: Third-party API integration

**Usage**:

```php
// Get mobile app orders
$appOrders = Order::where('order_from', 'app')->count();
```

---

### **Order Status**

#### `delivery_status` - Fulfillment Status

```sql
varchar(20) DEFAULT 'pending'
```

- **Purpose**: Tracks order fulfillment progress
- **Common Values**:
    - `'pending'`: Order placed, awaiting processing
    - `'confirmed'`: Seller confirmed the order
    - `'on_delivery'`: Order shipped/in transit
    - `'delivered'`: Order delivered to customer
    - `'cancelled'`: Order cancelled

**State Machine**:

```
pending → confirmed → on_delivery → delivered
    ↓
cancelled (can happen at any stage)
```

**Usage**:

```php
if ($order->delivery_status == 'delivered') {
    // Release payment to seller
    // Allow customer to review
}
```

---

#### `payment_status` - Payment State

```sql
varchar(20) DEFAULT 'unpaid'
```

- **Purpose**: Tracks payment collection
- **Values**:
    - `'unpaid'`: Payment not received
    - `'paid'`: Payment completed
    - `'refunded'`: Payment returned to customer

**Cash on Delivery (COD)**:

```php
if ($order->payment_type == 'cash_on_delivery' && $order->delivery_status == 'delivered') {
    $order->payment_status = 'paid';
    $order->save();
}
```

---

### **Payment Information**

#### `payment_type` - Payment Method

```sql
varchar(20) DEFAULT NULL
```

- **Purpose**: How customer paid
- **Common Values**:
    - `'cash_on_delivery'`
    - `'stripe'`
    - `'paypal'`
    - `'razorpay'`
    - `'wallet'` (store credit)

---

#### `payment_details` - Payment Metadata

```sql
longtext DEFAULT NULL
```

- **Purpose**: JSON-encoded payment gateway response
- **Format**:

```json
{
  "method": "stripe",
  "transaction_id": "ch_3K1x...",
  "card_last4": "4242",
  "card_brand": "visa",
  "amount": 99.99,
  "currency": "USD"
}
```

**Security**: Should NOT store full card numbers (PCI DSS violation)

---

### **Financial Data**

#### `grand_total` - Order Total Amount

```sql
double(20,2) DEFAULT NULL
```

- **Purpose**: Total order amount including tax, shipping, discounts
- **Calculation**:

```php
$grand_total = $subtotal + $tax + $shipping - $coupon_discount;
```

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for currency

**Fix**:

```sql
ALTER TABLE orders MODIFY grand_total DECIMAL(20,2) NULL;
ALTER TABLE orders MODIFY coupon_discount DECIMAL(20,2) NOT NULL DEFAULT 0.00;
```

---

#### `coupon_discount` - Discount Applied

```sql
double(20,2) NOT NULL DEFAULT 0.00
```

- **Purpose**: Amount deducted via coupon code
- **Usage**: Tracked for seller commission calculation

---

### **Order Tracking**

#### `code` - Order Reference Number

```sql
mediumtext DEFAULT NULL
```

- **Purpose**: Human-readable order identifier
- **Format**: Typically `ORD-{timestamp}-{random}`
- **Example**: `ORD-20240204-A7B3C`

**Customer Communication**:

```
"Your order ORD-20240204-A7B3C has been shipped"
```

---

#### `tracking_code` - Carrier Tracking Number

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: Shipping carrier's tracking number
- **Usage**: Customer can track package
- **Example**: `1Z999AA10123456784` (UPS)

---

#### `date` - Order Placement Date

```sql
int(20) NOT NULL
```

- **Purpose**: Unix timestamp when order was placed
- **Type**: Integer (should be TIMESTAMP)

**⚠️ Issue**: Using INT instead of TIMESTAMP

**Recommended Fix**:

```sql
ALTER TABLE orders MODIFY date TIMESTAMP NOT NULL DEFAULT current_timestamp();
```

---

### **Admin Tracking Flags**

#### `viewed` - Admin Viewed Flag

```sql
int(1) NOT NULL DEFAULT 0
```

- **Values**:
    - `0`: New order (not yet viewed by admin/seller)
    - `1`: Order has been viewed

**Usage**: Highlight new orders in dashboard

---

#### `delivery_viewed` - Delivery Status Viewed

```sql
int(1) NOT NULL DEFAULT 1
```

- **Purpose**: Track if admin/seller viewed delivery status change
- **Default**: 1 (already viewed)

---

#### `payment_status_viewed` - Payment Status Viewed

```sql
int(1) DEFAULT 1
```

- **Purpose**: Track if payment status change was viewed

---

#### `commission_calculated` - Commission Processed Flag

```sql
int(11) NOT NULL DEFAULT 0
```

- **Values**:
    - `0`: Commission not yet calculated
    - `1`: Commission calculated and recorded

**When Set**:

```php
if ($order->delivery_status == 'delivered' && $order->commission_calculated == 0) {
    // Calculate platform commission
    $commission = $order->grand_total * 0.10; // 10% commission
    Commission::create([
        'order_id' => $order->id,
        'seller_id' => $order->seller_id,
        'amount' => $commission,
    ]);
    
    $order->commission_calculated = 1;
    $order->save();
}
```

---

#### `notified` - Customer Notification Sent

```sql
tinyint(1) NOT NULL DEFAULT 0
```

- **Values**:
    - `0`: Notification not sent
    - `1`: Customer notified about order status

---

### **Timestamps**

#### `delivered_date` - Delivery Completion Time

```sql
timestamp NULL DEFAULT NULL
```

- **Purpose**: When order was marked as delivered
- **Usage**: Calculate delivery time, trigger review requests

---

#### `created_at`, `updated_at`

```sql
created_at TIMESTAMP NOT NULL DEFAULT current_timestamp()
updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp()
```

- **Auto-Managed**: Laravel handles these

---

## Relationships

### Belongs To Combined Order

```php
public function combinedOrder()
{
    return $this->belongsTo(CombinedOrder::class);
}
```

### Belongs To User (Customer)

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

### Belongs To Seller

```php
public function seller()
{
    return $this->belongsTo(User::class, 'seller_id');
}
```

### Has Many Order Details (Line Items)

```php
public function orderDetails()
{
    return $this->hasMany(OrderDetail::class);
}
```

### Has One Payment

```php
public function payment()
{
    return $this->hasOne(Payment::class);
}
```

---

## Common Queries

### Get Seller's Pending Orders

```php
Order::where('seller_id', $sellerId)
    ->where('delivery_status', 'pending')
    ->with('orderDetails.product')
    ->get();
```

### Get Unpaid Orders

```php
Order::where('payment_status', 'unpaid')
    ->where('created_at', '<', now()->subDays(7))
    ->get();
```

### Calculate Total Revenue for Seller

```php
$revenue = Order::where('seller_id', $sellerId)
    ->where('payment_status', 'paid')
    ->sum('grand_total');
```

### Get Orders Pending Commission

```php
Order::where('delivery_status', 'delivered')
    ->where('commission_calculated', 0)
    ->get();
```

---

## Business Logic

### Order Lifecycle

```php
// 1. Create Order
$order = Order::create([
    'combined_order_id' => $combinedOrder->id,
    'user_id' => auth()->id(),
    'seller_id' => $sellerId,
    'grand_total' => $total,
    'payment_type' => 'stripe',
    'payment_status' => 'unpaid',
    'delivery_status' => 'pending',
]);

// 2. Process Payment
$order->payment_status = 'paid';
$order->save();

// 3. Seller Confirms
$order->delivery_status = 'confirmed';
$order->save();

// 4. Mark as Shipped
$order->delivery_status = 'on_delivery';
$order->tracking_code = 'TRACK123';
$order->save();

// 5. Mark as Delivered
$order->delivery_status = 'delivered';
$order->delivered_date = now();
$order->save();

// 6. Calculate Commission
$this->calculateCommission($order);
```

---

## Security Considerations

### 1. Validate Order Ownership

```php
// Before showing order details
if ($order->user_id != auth()->id() && $order->seller_id != auth()->id() && !auth()->user()->is Admin()) {
    abort(403);
}
```

### 2. Prevent Status Manipulation

```php
// Only allow valid state transitions
public function updateDeliveryStatus($newStatus)
{
    $allowedTransitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['on_delivery', 'cancelled'],
        'on_delivery' => ['delivered'],
    ];
    
    if (!in_array($newStatus, $allowedTransitions[$this->delivery_status] ?? [])) {
        throw new \Exception('Invalid status transition');
    }
    
    $this->delivery_status = $newStatus;
    $this->save();
}
```

### 3. Audit Price Changes

**Issue**: No protection against `grand_total` being modified after order placement

**Solution**: Log all changes

```php
// Observer
public function updated(Order $order)
{
    if ($order->isDirty('grand_total')) {
        Log::warning('Order total changed', [
            'order_id' => $order->id,
            'old_total' => $order->getOriginal('grand_total'),
            'new_total' => $order->grand_total,
            'user_id' => auth()->id(),
        ]);
    }
}
```

---

## Refactoring Opportunities

### 1. Fix Data Types

```sql
ALTER TABLE orders MODIFY grand_total DECIMAL(20,2) NULL;
ALTER TABLE orders MODIFY coupon_discount DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE orders MODIFY date TIMESTAMP NOT NULL DEFAULT current_timestamp();
```

### 2. Add Foreign Keys

```sql
ALTER TABLE orders ADD CONSTRAINT fk_order_combined FOREIGN KEY (combined_order_id) REFERENCES combined_orders(id);
ALTER TABLE orders ADD CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE orders ADD CONSTRAINT fk_order_seller FOREIGN KEY (seller_id) REFERENCES users(id);
```

### 3. Normalize Address Data

**Current**: Address JSON stored per order

**Proposed**: Reference `addresses` table

```sql
ALTER TABLE orders ADD COLUMN shipping_address_id INT NULL;
ALTER TABLE orders ADD CONSTRAINT fk_order_address FOREIGN KEY (shipping_address_id) REFERENCES addresses(id);
```

### 4. Use ENUMs for Status Fields

```sql
ALTER TABLE orders MODIFY delivery_status ENUM('pending', 'confirmed', 'on_delivery', 'delivered', 'cancelled') DEFAULT 'pending';
ALTER TABLE orders MODIFY payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid';
```

### 5. Add Indexes

```sql
CREATE INDEX idx_seller_status ON orders(seller_id, delivery_status);
CREATE INDEX idx_payment_status ON orders(payment_status);
CREATE INDEX idx_combined_order ON orders(combined_order_id);
CREATE INDEX idx_delivery_commission ON orders(delivery_status, commission_calculated);
```

---

## Related Documentation

- [Order Details Table](./order_details_table_reference.md)
- [Combined Orders Table](./combined_orders_table_reference.md)
- [Products Table](./products_table_reference.md)
- [Users Table](./users_table_reference.md)
- [Sellers Table](./sellers_table_reference.md)
