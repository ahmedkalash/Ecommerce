# Sellers Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `sellers`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `sellers` table stores vendor-specific data for users who sell products on the multi-vendor e-commerce platform. It extends the `users` table with seller-specific metrics and verification status.

**Architecture Pattern**: **Single Table Inheritance (STI) Extension**
- Main user data → `users` table (`user_type = 'Seller'`)
- Seller-specific data → `sellers` table (this table)
- Shop data → `shops` table (one seller can have one shop)

**Key Features**:
- Seller verification workflow
- Performance metrics (reviews, ratings, sales)
- Admin approval process

**Related Tables**:
- `users`: Authentication and basic profile
- `shops`: Seller's storefront (1:1 relationship)
- `products`: Products listed by the seller
- `orders`: Orders fulfilled by the seller
- `reviews`: Customer reviews for seller's products

---

## Column Specifications

### **Primary Key**

#### `id` - Seller Profile ID
```sql
int(11) NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for the seller profile
- **Type**: Auto-incrementing integer
- **Usage**: Referenced in `products`, `orders`, `commission_histories`, etc.

**Note**: This is the seller ID, NOT the user ID. The user ID is stored in `user_id`.

---

### **Foreign Keys**

#### `user_id` - Link to Users Table
```sql
int(11) NOT NULL
```
- **Purpose**: Foreign key to `users.id`
- **Constraint**: Must reference a user where `user_type = 'Seller'` (note the capital S)
- **Uniqueness**: One user can have only ONE seller profile (1:1 relationship)

**Usage**:
```php
$seller = Seller::find(1);
$seller->user->name; // Get seller's name
$seller->user->email; // Get seller's email
```

**⚠️ Critical Issues**:
1. No database foreign key constraint
2. No unique constraint (could allow duplicate sellers for one user)
3. `user_type` ENUM has inconsistent casing (`'Seller'` with capital S)

**Recommended Fix**:
```sql
ALTER TABLE sellers 
ADD CONSTRAINT fk_seller_user 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE sellers 
ADD UNIQUE KEY unique_seller_user (user_id);
```

---

### **Performance Metrics**

#### `rating` - Average Seller Rating
```sql
double(3,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Calculated average rating from customer reviews
- **Range**: 0.00 to 5.00 (assuming 5-star system)
- **Precision**: 2 decimal places (e.g., 4.73)
- **Calculation**: Average of all product reviews for this seller

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for ratings can cause precision errors

**Example Calculation**:
```php
// When new review is added
$totalReviews = $seller->num_of_reviews + 1;
$newRating = (($seller->rating * $seller->num_of_reviews) + $newReviewRating) / $totalReviews;

$seller->rating = $newRating;
$seller->num_of_reviews = $totalReviews;
$seller->save();
```

**Recommended Fix**:
```sql
ALTER TABLE sellers MODIFY rating DECIMAL(3,2) NOT NULL DEFAULT 0.00;
```

**Usage in Frontend**:
```blade
{{-- Display star rating --}}
<div class="rating">
    @for($i = 1; $i <= 5; $i++)
        @if($i <= floor($seller->rating))
            <i class="fas fa-star"></i>
        @elseif($i - $seller->rating < 1)
            <i class="fas fa-star-half-alt"></i>
        @else
            <i class="far fa-star"></i>
        @endif
    @endfor
    <span>({{ $seller->rating }})</span>
</div>
```

---

####  `num_of_reviews` - Total Review Count
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Total number of reviews the seller has received
- **Type**: Integer counter
- **Usage**: Display credibility (e.g., "4.5 stars from 234 reviews")

**Increment Logic**:
```php
// When a new review is created
$seller = $product->seller;
$seller->increment('num_of_reviews');

// When review is deleted
$seller->decrement('num_of_reviews');

// Recalculate from scratch (for accuracy)
$seller->num_of_reviews = Review::whereHas('product', function ($q) use ($seller) {
    $q->where('user_id', $seller->user_id);
})->count();
$seller->save();
```

**Display Example**:
```blade
{{ number_format($seller->num_of_reviews) }} reviews
```

---

#### `num_of_sale` - Total Sales Count
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Total number of completed orders/sales
- **Type**: Integer counter
- **Business Logic**: Incremented when order is marked as "delivered"

**Increment Logic**:
```php
// When order is marked as delivered
$orderDetails = OrderDetail::where('order_id', $order->id)
    ->where('seller_id', $sellerId)
    ->get();

if ($orderDetails->count() > 0) {
    $seller = Seller::where('user_id', $sellerId)->first();
    $seller->num_of_sale += $orderDetails->sum('quantity'); // Or count orders
    $seller->save();
}
```

**Usage**:
```blade
{{-- Seller credibility badge --}}
@if($seller->num_of_sale > 1000)
    <span class="badge badge-gold">Top Seller ({{ number_format($seller->num_of_sale) }} sales)</span>
@elseif($seller->num_of_sale > 100)
    <span class="badge badge-silver">Trusted Seller</span>
@endif
```

**⚠️ Data Integrity Issue**: Counter can drift from actual completed orders if not recalculated periodically.

**Audit Query**:
```sql
SELECT s.id, s.user_id, s.num_of_sale as recorded_sales, 
       COUNT(DISTINCT od.order_id) as actual_sales
FROM sellers s
LEFT JOIN order_details od ON od.seller_id = s.user_id AND od.delivery_status = 'delivered'
GROUP BY s.id
HAVING recorded_sales != actual_sales;
```

---

### **Verification System**

#### `verification_status` - Seller Approval Status
```sql
int(1) NOT NULL DEFAULT 0
```
- **Purpose**: Admin verification/approval status for the seller
- **Type**: Boolean-like integer (0 or 1)

**Values**:
- `0`: **Pending** - Seller registered but not yet verified by admin
- `1`: **Verified** - Seller approved by admin, can list products

**Business Flow**:
```
1. User registers as seller → verification_status = 0
2. Admin reviews verification_info
3. Admin approves → verification_status = 1
4. Seller can now create products and receive orders
```

**Usage**:
```php
// Check if seller is verified
if ($seller->verification_status == 0) {
    return redirect()->route('seller.pending')->with('info', 'Your account is pending admin approval');
}

// Admin approval action
$seller->verification_status = 1;
$seller->save();

// Send notification
event(new SellerVerified($seller));
```

**Middleware Example**:
```php
// app/Http/Middleware/VerifiedSeller.php
public function handle($request, Closure $next)
{
    if (auth()->user()->seller->verification_status != 1) {
        abort(403, 'Your seller account is not verified');
    }
    
    return $next($request);
}
```

**⚠️ Issue**: No "rejected" status (only pending or approved). Consider using ENUM:
```sql
ALTER TABLE sellers MODIFY verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending';
```

---

#### `verification_info` - Verification Documents/Data
```sql
longtext DEFAULT NULL
```
- **Purpose**: Stores seller verification documents or serialized data
- **Type**: LONGTEXT (can store large amounts of text/JSON)
- **Nullable**: Yes (sellers might not submit verification info)

**Common Uses**:
1. **File paths to verification documents**:
   ```json
   {
       "business_license": "/uploads/verification/license_123.pdf",
       "identity_proof": "/uploads/verification/id_456.jpg",
       "tax_id": "12-3456789",
       "bank_account": "XXXX-XXXX-1234"
   }
   ```

2. **Business information**:
   ```json
   {
       "business_name": "ABC Electronics",
       "business_type": "LLC",
       "registration_number": "REG123456",
       "address": "123 Main St, City, State",
       "phone": "+1234567890"
   }
   ```

3. **Admin notes**:
   ```text
   Verified on 2024-01-15 by Admin User #5
   Documents checked: Business license, Tax ID
   Notes: All documents valid
   ```

**Storage Recommendation**: Use JSON for structured data
```php
// Storing
$seller->verification_info = json_encode([
    'business_license' => $request->file('license')->store('verification'),
    'tax_id' => $request->tax_id,
    'submitted_at' => now(),
]);
$seller->save();

// Retrieving
$info = json_decode($seller->verification_info, true);
$licensePath = $info['business_license'];
```

**Security Concern**: Sensitive data (tax IDs, banking info) stored as plaintext

**Recommended Fix**: Encrypt sensitive fields
```php
use Illuminate\Support\Facades\Crypt;

$seller->verification_info = Crypt::encryptString(json_encode($data));

// Decrypt when needed
$data = json_decode(Crypt::decryptString($seller->verification_info), true);
```

---

### **Timestamps**

#### `updated_at` - Last Update Timestamp
```sql
timestamp NOT NULL DEFAULT current_timestamp()
```
- **Purpose**: Tracks when seller profile was last modified
- **Auto-Updated**: Yes, on any save/update

**⚠️ Missing Column**: No `created_at` timestamp

**Recommended Fix**:
```sql
ALTER TABLE sellers 
ADD COLUMN created_at TIMESTAMP NULL DEFAULT current_timestamp() AFTER verification_info;
```

---

## Relationships

### Belongs To User
```php
// In Seller model
public function user()
{
    return $this->belongsTo(User::class);
}
```

**Usage**:
```php
$seller = Seller::find(1);
$seller->user->name;
$seller->user->email;
$seller->user->banned; // Check if seller account is banned
```

---

### Has One Shop
```php
// In Seller model
public function shop()
{
    return $this->hasOne(Shop::class, 'user_id', 'user_id');
}
```

**Usage**:
```php
$seller = Seller::find(1);
$seller->shop->name; // Shop name
$seller->shop->slug; // Shop URL slug
$seller->shop->logo; // Shop logo
```

---

### Has Many Products
```php
// In Seller model
public function products()
{
    return $this->hasMany(Product::class, 'user_id', 'user_id');
}
```

**Usage**:
```php
$seller = Seller::find(1);
$seller->products; // All products listed by this seller
$seller->products()->where('published', 1)->count(); // Published products
```

---

### Has Many Orders (via OrderDetails)
```php
// In Seller model
public function orders()
{
    return $this->hasManyThrough(
        Order::class,
        OrderDetail::class,
        'seller_id',    // Foreign key on order_details
        'id',           // Foreign key on orders
        'user_id',      // Local key on sellers
        'order_id'      // Local key on order_details
    );
}
```

**Usage**:
```php
$seller = Seller::find(1);
$seller->orders; // All orders containing this seller's products
```

---

## Business Logic

### Seller Registration Flow
```php
// 1. Create User
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'user_type' => 'Seller', // Capital S - matches ENUM
]);

// 2. Create Seller Profile
$seller = Seller::create([
    'user_id' => $user->id,
    'rating' => 0.00,
    'num_of_reviews' => 0,
    'num_of_sale' => 0,
    'verification_status' => 0, // Pending
]);

// 3. Create Shop
$shop = Shop::create([
    'user_id' => $user->id,
    'name' => $request->shop_name,
    'slug' => Str::slug($request->shop_name),
]);

return $seller;
```

---

### Seller Verification Process
```php
// Seller submits verification
public function submitVerification(Request $request)
{
    $seller = auth()->user()->seller;
    
    $verificationData = [
        'business_license' => $request->file('license')->store('verification'),
        'tax_id' => $request->tax_id,
        'submitted_at' => now(),
    ];
    
    $seller->verification_info = json_encode($verificationData);
    $seller->save();
    
    // Notify admin
    Notification::send(User::role('Admin')->get(), new NewSellerVerificationRequest($seller));
}

// Admin approves
public function approve(Seller $seller)
{
    $seller->verification_status = 1;
    $seller->save();
    
    // Notify seller
    $seller->user->notify(new SellerVerified());
    
    return redirect()->back()->with('success', 'Seller approved');
}
```

---

### Update Seller Metrics
```php
// When new review is added
public function updateRating(Seller $seller, Review $review)
{
    $totalReviews = $seller->num_of_reviews + 1;
    $newRating = (($seller->rating * $seller->num_of_reviews) + $review->rating) / $totalReviews;
    
    $seller->rating = round($newRating, 2);
    $seller->num_of_reviews = $totalReviews;
    $seller->save();
}

// When order is delivered
public function incrementSales(Seller $seller, OrderDetail $orderDetail)
{
    $seller->num_of_sale += $orderDetail->quantity;
    $seller->save();
}
```

---

## Common Queries

### Get Top Rated Sellers
```php
$topSellers = Seller::where('verification_status', 1)
    ->where('num_of_reviews', '>', 10) // Minimum reviews for credibility
    ->orderBy('rating', 'desc')
    ->take(10)
    ->with('user', 'shop')
    ->get();
```

### Get Pending Verification Sellers
```php
$pendingSellers = Seller::where('verification_status', 0)
    ->whereNotNull('verification_info')
    ->with('user')
    ->get();
```

### Get Sellers by Sales Volume
```php
$topSellingVendors = Seller::where('verification_status', 1)
    ->orderBy('num_of_sale', 'desc')
    ->take(20)
    ->get();
```

### Recalculate Seller Metrics (Audit/Fix)
```php
// Fix rating and review count
$seller = Seller::find($sellerId);

$reviews = Review::whereHas('product', function ($q) use ($seller) {
    $q->where('user_id', $seller->user_id);
})->get();

$seller->rating = $reviews->avg('rating') ?? 0;
$seller->num_of_reviews = $reviews->count();
$seller->save();
```

---

## Security Considerations

### 1. Verification Document Access
**Risk**: Verification documents contain sensitive PII

**Protection**:
```php
// Only admins and the seller themselves can view
public function downloadVerificationDoc($sellerId, $documentType)
{
    $seller = Seller::findOrFail($sellerId);
    
    if (!auth()->user()->hasRole('Admin') && auth()->id() != $seller->user_id) {
        abort(403);
    }
    
    // Return document
}
```

### 2. Prevent Seller Impersonation
**Risk**: User could create multiple seller accounts

**Detection**:
```sql
-- Find users with multiple seller profiles (should be impossible with unique constraint)
SELECT user_id, COUNT(*) as seller_count
FROM sellers
GROUP BY user_id
HAVING seller_count > 1;
```

### 3. Metric Manipulation Prevention
**Risk**: Sellers might try to inflate ratings/sales

**Audit**:
```php
// Daily cron job to verify metrics
public function auditSellerMetrics()
{
    $sellers = Seller::all();
    
    foreach ($sellers as $seller) {
        $actualReviews = Review::whereHas('product', fn($q) => $q->where('user_id', $seller->user_id))->count();
        
        if ($actualReviews != $seller->num_of_reviews) {
            Log::warning('Seller metric mismatch', [
                'seller_id' => $seller->id,
                'recorded' => $seller->num_of_reviews,
                'actual' => $actualReviews
            ]);
        }
    }
}
```

---

## Refactoring Opportunities

### 1. Add Missing Constraints
```sql
-- Foreign key
ALTER TABLE sellers ADD CONSTRAINT fk_seller_user 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Unique constraint
ALTER TABLE sellers ADD UNIQUE KEY unique_seller_user (user_id);

-- Add created_at
ALTER TABLE sellers ADD COLUMN created_at TIMESTAMP NULL DEFAULT current_timestamp();
```

### 2. Fix Data Types
```sql
-- Use DECIMAL for rating
ALTER TABLE sellers MODIFY rating DECIMAL(3,2) NOT NULL DEFAULT 0.00;

-- Use ENUM for verification_status
ALTER TABLE sellers MODIFY verification_status ENUM('pending', 'approved', 'rejected', 'suspended') DEFAULT 'pending';
```

### 3. Separate Verification Data
**Current**: verification_info is a blob of JSON

**Proposed**: Create `seller_verifications` table
```sql
CREATE TABLE seller_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    business_license_path VARCHAR(255),
    identity_proof_path VARCHAR(255),
    tax_id VARCHAR(100),
    bank_account VARCHAR(100),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT current_timestamp(),
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE
);
```

### 4. Add Performance Indexes
```sql
CREATE INDEX idx_verification_status ON sellers(verification_status);
CREATE INDEX idx_rating ON sellers(rating) WHERE verification_status = 1;
CREATE INDEX idx_sales ON sellers(num_of_sale) WHERE verification_status = 1;
```

---

## Related Documentation

- [Users Table](./users_table_reference.md)
- [Shops Table](./shops_table_reference.md)
- [Products Table](./products_table_reference.md)
- [Orders Table](./orders_table_reference.md)
- [Auth Module Analysis](../Auth_Module_Analysis.md)
