# Carts Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `carts`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `carts` table stores shopping cart items for both registered users and guest shoppers. Each row represents **ONE product** (with specific variation) added to a cart.

**Key Concept**: This is NOT a "cart header" table. Each row is a **cart line item** (similar to order_details for orders).

**Cart Architecture**:
- **Registered User**: Items linked via `user_id`
- **Guest User**: Items linked via `temp_user_id` (session/cookie identifier)
- **Status**: Active carts (`status = 1`) vs. abandoned/checked-out carts (`status = 0`)

**Related Tables**:
- `products`: Product being added to cart
- `users`: Cart owner (if registered)
- `addresses`: Delivery address selected
- `coupons`: Applied discount coupons

---

## Column Specifications

### **Primary Key**

#### `id` - Cart Item ID
```sql
int(11) unsigned NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for each cart item
- **Usage**: Update quantity, remove item, apply coupons

---

### **Cart Status**

#### `status` - Cart Active Flag
```sql
tinyint(1) NOT NULL DEFAULT 1
```
- **Values**:
  - `1`: Active cart (items still in cart)
  - `0`: Inactive (checked out or abandoned)

**When Set to 0**:
```php
// After successful checkout
Cart::where('user_id', auth()->id())->update(['status' => 0]);
```

**Usage**:
```php
// Get active cart items
$cartItems = Cart::where('user_id', auth()->id())
    ->where('status', 1)
    ->get();
```

---

### **Cart Ownership**

#### `owner_id` - Seller/Shop Owner
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `users.id` (the seller who owns the product)
- **Usage**: Group cart items by seller for split checkout

**Multi-Vendor Grouping**:
```php
// Group cart by seller
$cartBySeller = Cart::where('user_id', auth()->id())
    ->where('status', 1)
    ->get()
    ->groupBy('owner_id');

foreach ($cartBySeller as $sellerId => $items) {
    // Create separate order for each seller
}
```

---

#### `user_id` - Registered User
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `users.id` (registered customer)
- **NULL**: For guest carts (use `temp_user_id` instead)

**Usage**:
```php
// Add to cart for logged-in user
Cart::create([
    'user_id' => auth()->id(),
    'product_id' => $productId,
    'quantity' => $quantity,
]);
```

---

#### `temp_user_id` - Guest Session Identifier
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Session/cookie ID for guest shoppers
- **Format**: Typically a UUID or session token

**Guest Cart Logic**:
```php
// Generate temp ID for guest
if (!auth()->check()) {
    $tempUserId = session()->getId(); // Or generate UUID
    
    Cart::create([
        'temp_user_id' => $tempUserId,
        'product_id' => $productId,
        'quantity' => $quantity,
    ]);
}
```

**Merge Carts After Login**:
```php
// When guest logs in, merge temp cart to user cart
public function mergeGuestCart($tempUserId)
{
    Cart::where('temp_user_id', $tempUserId)
        ->update([
            'user_id' => auth()->id(),
            'temp_user_id' => null,
        ]);
}
```

---

### **Product Information**

#### `product_id` - Product Reference
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `products.id`
- **Usage**: Display product details, images, name

**Soft Deleted Products**:
```php
// Check if product still exists
$cartItem = Cart::find(1);
if (!$cartItem->product) {
    // Product deleted, remove from cart
    $cartItem->delete();
}
```

---

#### `variation` - Product Variant
```sql
text DEFAULT NULL
```
- **Purpose**: JSON string defining selected variant
- **Format**:
```json
"{\"Size\":\"L\", \"Color\":\"Red\"}"
```

**Usage**:
```php
$variation = json_decode($cartItem->variation, true);
echo "Size: {$variation['Size']}, Color: {$variation['Color']}";
```

**Simple Products**: NULL (no variants)

---

#### `quantity` - Item Quantity
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Number of units in cart
- **Validation**: Must respect `products.min_qty` and `products.current_stock`

**Update Logic**:
```php
// Increase quantity
$cartItem->increment('quantity');

// Validate against stock
if ($cartItem->quantity > $cartItem->product->current_stock) {
    return back()->withErrors('Insufficient stock');
}
```

---

### **Pricing & Discounts**

#### `price` - Unit Price
```sql
double(20,2) DEFAULT 0.00
```
- **Purpose**: Price per unit at time of adding to cart
- **Note**: Frozen price (doesn't auto-update if product price changes)

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for currency

**Fix**:
```sql
ALTER TABLE carts MODIFY price DECIMAL(20,2) DEFAULT 0.00;
ALTER TABLE carts MODIFY tax DECIMAL(20,2) DEFAULT 0.00;
ALTER TABLE carts MODIFY shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE carts MODIFY discount DECIMAL(10,2) NOT NULL DEFAULT 0.00;
```

---

#### `discount` - Item Discount
```sql
double(10,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Discount amount applied to this item
- **Source**: Product discount or flash deal

---

#### `tax` - Tax Amount
```sql
double(20,2) DEFAULT 0.00
```
- **Purpose**: Calculated tax for this cart item
- **Calculation**:
```php
$tax = $cartItem->price * ($product->tax / 100) * $cartItem->quantity;
```

---

### **Shipping**

#### `shipping_cost` - Estimated Shipping Fee
```sql
double(20,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Shipping cost calculated for this item
- **Calculation**: Based on `product.shipping_cost` and `quantity`

---

#### `shipping_type` - Delivery Method
```sql
varchar(30) NOT NULL DEFAULT ''
```
- **Values**:
  - `'free'`: Free shipping
  - `'flat_rate'`: Fixed cost
  - `'carrier'`: Carrier-based
  - `'pickup_point'`: Customer pickup

---

#### `pickup_point` - Pickup Location
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `pickup_points.id`
- **Usage**: If `shipping_type = 'pickup_point'`

---

#### `carrier_id` - Shipping Carrier
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `carriers.id`
- **Usage**: If `shipping_type = 'carrier'`

---

#### `address_id` - Delivery Address
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Foreign key to `addresses.id`
- **Usage**: Pre-selected delivery address during cart browsing
- **Default**: 0 (not yet selected)

**Typical Flow**:
```
1. User adds item to cart (address_id = 0)
2. User goes to checkout, selects address
3. Update cart items: address_id = selected address
4. Calculate final shipping based on address
```

---

### **Promotions & Referrals**

#### `product_referral_code` - Affiliate/Referral Code
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Tracks affiliate who referred this product
- **Usage**: Award commission to affiliate on purchase

**Example**:
```
URL: /product/123?ref=AFFILIATE_CODE
Cart item stored with: product_referral_code = 'AFFILIATE_CODE'
```

---

#### `coupon_code` - Applied Coupon
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Coupon code applied to this cart item
- **Usage**: Track which items get coupon discount

---

#### `coupon_applied` - Coupon Status Flag
```sql
tinyint(4) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: No coupon applied
  - `1`: Coupon successfully applied

**Validation**:
```php
// Apply coupon
$coupon = Coupon::where('code', $request->coupon)->first();

if ($coupon && $coupon->isValid()) {
    $cartItem->coupon_code = $coupon->code;
    $cartItem->coupon_applied = 1;
    $cartItem->discount = $this->calculateCouponDiscount($coupon, $cartItem);
    $cartItem->save();
}
```

---

### **Timestamps**

#### `created_at`, `updated_at`
```sql
created_at TIMESTAMP NULL DEFAULT current_timestamp()
updated_at TIMESTAMP NULL DEFAULT current_timestamp()
```
- **Usage**: Track cart age, identify abandoned carts

**Abandoned Cart Query**:
```php
// Find carts abandoned for > 24 hours
$abandonedCarts = Cart::where('status', 1)
    ->where('updated_at', '<', now()->subHours(24))
    ->whereNotNull('user_id') // Only registered users
    ->get()
    ->unique('user_id');

// Send reminder emails
foreach ($abandonedCarts as $cart) {
    Mail::to($cart->user->email)->send(new AbandonedCartReminder($cart));
}
```

---

## Relationships

### Belongs To User
```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

### Belongs To Product
```php
public function product()
{
    return $this->belongsTo(Product::class);
}
```

### Belongs To Address
```php
public function address()
{
    return $this->belongsTo(Address::class);
}
```

---

## Common Queries

### Get Active Cart for User
```php
$cart = Cart::where('user_id', auth()->id())
    ->where('status', 1)
    ->with('product')
    ->get();
```

### Get Guest Cart
```php
$tempUserId = session()->getId();
$cart = Cart::where('temp_user_id', $tempUserId)
    ->where('status', 1)
    ->get();
```

### Calculate Cart Total
```php
$cartTotal = Cart::where('user_id', auth()->id())
    ->where('status', 1)
    ->get()
    ->sum(function ($item) {
        return ($item->price - $item->discount) * $item->quantity + $item->tax + $item->shipping_cost;
    });
```

### Group Cart by Seller
```php
$cartBySeller = Cart::where('user_id', auth()->id())
    ->where('status', 1)
    ->with('product.user.shop')
    ->get()
    ->groupBy('owner_id');
```

---

## Business Logic

### Add to Cart
```php
public function addToCart($productId, $quantity, $variation = null)
{
    $product = Product::findOrFail($productId);
    
    // Check if item already exists
    $existingCartItem = Cart::where('user_id', auth()->id())
        ->where('product_id', $productId)
        ->where('variation', $variation)
        ->where('status', 1)
        ->first();
    
    if ($existingCartItem) {
        // Update quantity
        $existingCartItem->quantity += $quantity;
        $existingCartItem->save();
    } else {
        // Create new cart item
        Cart::create([
            'user_id' => auth()->id(),
            'owner_id' => $product->user_id,
            'product_id' => $productId,
            'variation' => $variation,
            'price' => $product->unit_price,
            'quantity' => $quantity,
            'tax' => $this->calculateTax($product),
            'shipping_cost' => $product->shipping_cost,
        ]);
    }
}
```

### Checkout Process
```php
public function checkout()
{
    $cartItems = Cart::where('user_id', auth()->id())
        ->where('status', 1)
        ->get()
        ->groupBy('owner_id');
    
    foreach ($cartItems as $sellerId => $items) {
        // Create order for each seller
        $order = Order::create([
            'user_id' => auth()->id(),
            'seller_id' => $sellerId,
            'grand_total' => $this->calculateSellerTotal($items),
        ]);
        
        // Create order details from cart items
        foreach ($items as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }
    }
    
    // Mark cart as inactive
    Cart::where('user_id', auth()->id())->update(['status' => 0]);
}
```

---

## Security Considerations

### 1. Validate Cart Ownership
```php
// Before modifying cart item
$cartItem = Cart::findOrFail($id);

if ($cartItem->user_id != auth()->id() && $cartItem->temp_user_id != session()->getId()) {
    abort(403);
}
```

### 2. Prevent Price Manipulation
**Issue**: Prices stored in cart can be manually edited in DB

**Protection**:
```php
// Always recalculate prices at checkout
foreach ($cartItems as $item) {
    $currentPrice = $item->product->unit_price;
    
    if ($item->price != $currentPrice) {
        Log::warning('Cart price mismatch', [
            'cart_id' => $item->id,
            'stored_price' => $item->price,
            'current_price' => $currentPrice,
        ]);
        
        // Use current price
        $item->price = $currentPrice;
        $item->save();
    }
}
```

### 3. Stock Validation at Checkout
```php
foreach ($cartItems as $item) {
    if ($item->quantity > $item->product->current_stock) {
        return back()->withErrors("Insufficient stock for {$item->product->name}");
    }
}
```

---

## Refactoring Opportunities

### 1. Fix Data Types
```sql
ALTER TABLE carts MODIFY price DECIMAL(20,2) DEFAULT 0.00;
ALTER TABLE carts MODIFY tax DECIMAL(20,2) DEFAULT 0.00;
ALTER TABLE carts MODIFY shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE carts MODIFY discount DECIMAL(10,2) NOT NULL DEFAULT 0.00;
```

### 2. Add Foreign Keys
```sql
ALTER TABLE carts ADD CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE carts ADD CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;
ALTER TABLE carts ADD CONSTRAINT fk_cart_address FOREIGN KEY (address_id) REFERENCES addresses(id);
```

### 3. Add Composite Unique Index
**Prevent duplicate cart items**:
```sql
CREATE UNIQUE INDEX idx_unique_cart_item 
ON carts(user_id, product_id, variation(100), status) 
WHERE user_id IS NOT NULL;
```

### 4. Auto-Cleanup Abandoned Carts
**Cron Job**:
```php
// Delete inactive carts older than 30 days
Cart::where('status', 0)
    ->where('updated_at', '<', now()->subDays(30))
    ->delete();
```

### 5. Add Indexes for Performance
```sql
CREATE INDEX idx_user_status ON carts(user_id, status);
CREATE INDEX idx_temp_user_status ON carts(temp_user_id, status);
CREATE INDEX idx_owner ON carts(owner_id);
CREATE INDEX idx_abandoned ON carts(status, updated_at);
```

---

## Related Documentation

- [Products Table](./products_table_reference.md)
- [Orders Table](./orders_table_reference.md)
- [Users Table](./users_table_reference.md)
- [Addresses Table](./addresses_table_reference.md)
- [Coupons Table](./coupons_table_reference.md)
