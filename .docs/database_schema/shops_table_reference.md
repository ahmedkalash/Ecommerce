# Shops Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `shops`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `shops` table represents seller storefronts in the multi-vendor e-commerce marketplace. Each seller (vendor) has **one shop** that acts as their branded storefront, containing their products, branding, and settings.

**Relationship**: **1:1 with sellers** (`users` table where `user_type = 'Seller'`)

**Key Features**:
- Custom branding (logo, banners, sliders)
- Social media links
- Seller verification and approval
- Package/subscription management
- Payment settings and commission tracking
- SEO optimization
- Geolocation for pickup/delivery

**Related Tables**:
- `users` / `sellers`: Shop owner
- `products`: Products listed in this shop
- `seller_packages`: Subscription plans
- `orders`: Orders from this shop

---

## Column Specifications

### **Primary Key**

#### `id` - Shop ID
```sql
int(11) NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for the shop
- **Usage**: Referenced in products, orders, reviews

---

### **Core Shop Information**

#### `user_id` - Seller/Owner ID
```sql
int(11) NOT NULL
```
- **Purpose**: Foreign key to `users.id` (the seller who owns this shop)
- **Uniqueness**: One user can have only ONE shop (1:1 relationship)

**Usage**:
```php
$shop = Shop::find(1);
$shop->user->name; // Seller's name
$shop->user->email; // Seller's email
```

**⚠️ Missing Constraints**:
```sql
ALTER TABLE shops ADD CONSTRAINT fk_shop_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE shops ADD UNIQUE KEY unique_shop_user (user_id);
```

---

#### `name` - Shop Name
```sql
varchar(200) DEFAULT NULL
```
- **Purpose**: Public shop name/brand
- **Example**: "TechGear Electronics", "Fashion Boutique"
- **Display**: Shop page title, seller badge on products

---

#### `slug` - URL Slug
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: SEO-friendly URL identifier
- **Example**: `techgear-electronics`
- **Usage**: `/shop/techgear-electronics`

**⚠️ Should Be Unique**:
```sql
ALTER TABLE shops ADD UNIQUE KEY unique_shop_slug (slug);
```

---

#### `phone` - Contact Phone
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Shop contact number
- **Display**: Shop page, for customer inquiries

---

#### `address` - Shop Address
```sql
varchar(500) DEFAULT NULL
```
- **Purpose**: Physical shop address
- **Usage**: Display on shop page, calculate shipping

---

###  **Branding Assets**

#### `logo` - Shop Logo
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Upload ID or file path for shop logo
- **Display**: Shop page, product listings, badges

---

#### `sliders` - Homepage Sliders (Legacy)
```sql
longtext DEFAULT NULL
```
- **Purpose**: Comma-separated upload IDs for slider images
- **Format**: `"123,456,789"`
- **Status**: **DEPRECATED** (replaced by `slider_images` and `slider_links`)

---

#### `slider_images`, `slider_links` - Shop Homepage Sliders
```sql
slider_images LONGTEXT DEFAULT NULL
slider_links LONGTEXT DEFAULT NULL
```
- **Purpose**: JSON arrays of slider images and their links
- **Format**:
```json
// slider_images
["upload_id_1", "upload_id_2", "upload_id_3"]

// slider_links
["https://example.com/sale", "/category/electronics", null]
```

---

#### Banner Columns
Multiple banner fields for shop page customization:

```sql
top_banner VARCHAR(191) DEFAULT NULL
top_banner_image LONGTEXT DEFAULT NULL
top_banner_link LONGTEXT DEFAULT NULL

banner_full_width_1 VARCHAR(191) DEFAULT NULL
banner_full_width_1_images LONGTEXT DEFAULT NULL
banner_full_width_1_links LONGTEXT DEFAULT NULL

banners_half_width VARCHAR(191) DEFAULT NULL
banners_half_width_images LONGTEXT DEFAULT NULL
banners_half_width_links LONGTEXT DEFAULT NULL

banner_full_width_2 VARCHAR(191) DEFAULT NULL
banner_full_width_2_images LONGTEXT DEFAULT NULL
banner_full_width_2_links LONGTEXT DEFAULT NULL
```

**Purpose**: Customizable banner sections for shop page layout

**Pattern**:
- `*_images`: JSON array of upload IDs
- `*_links`: JSON array of URLs (nullable for non-clickable banners)

---

### **Social Media Links**

```sql
facebook VARCHAR(255) DEFAULT NULL
instagram VARCHAR(255) DEFAULT NULL
google VARCHAR(255) DEFAULT NULL
twitter VARCHAR(255) DEFAULT NULL
youtube VARCHAR(255) DEFAULT NULL
```

**Purpose**: Shop's social media profiles
**Display**: Shop page footer/header

**Example**:
```php
@if($shop->facebook)
    <a href="{{ $shop->facebook }}"><i class="fab fa-facebook"></i></a>
@endif
```

---

### **Performance Metrics**

#### `rating` - Average Shop Rating
```sql
double(3,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Calculated average rating from product reviews
- **Range**: 0.00 to 5.00

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for ratings

**Fix**:
```sql
ALTER TABLE shops MODIFY rating DECIMAL(3,2) NOT NULL DEFAULT 0.00;
```

---

#### `num_of_reviews` - Total Review Count
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Total reviews across all shop products
- **Usage**: Display credibility

---

#### `num_of_sale` - Total Sales Count
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Total completed orders from this shop
- **Usage**: "Bestseller" badges, seller rankings

---

### **Verification & Approval**

#### `registration_approval` - Shop Approval Status
```sql
tinyint(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Pending admin approval
  - `1`: Approved and active

---

#### `verification_status` - Seller Verification
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Unverified
  - `1`: Verified by admin

**Note**: May duplicate `sellers.verification_status`

---

#### `verification_info` - Verification Documents
```sql
longtext DEFAULT NULL
```
- **Purpose**: JSON-encoded verification documents/info
- **Security**: Contains sensitive PII (should be encrypted)

---

### **Seller Package/Subscription**

#### `seller_package_id` - Active Package
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `seller_packages.id`
- **Usage**: Determines seller's features and product limits

---

#### `product_upload_limit` - Max Products Allowed
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Maximum number of products seller can list
- **Source**: Set by `seller_package`

**Validation**:
```php
if ($shop->products()->count() >= $shop->product_upload_limit) {
    return back()->withErrors('Product limit reached. Upgrade your package.');
}
```

---

#### `package_invalid_at` - Package Expiration Date
```sql
date DEFAULT NULL
```
- **Purpose**: When seller package expires
- **Usage**: Auto-downgrade or restrict features after expiration

**Check**:
```php
if ($shop->package_invalid_at && $shop->package_invalid_at < today()) {
    // Package expired
    $shop->downgradeToFreePackage();
}
```

---

### **Payment & Commission**

#### `cash_on_delivery_status` - COD Availability
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: COD not allowed for this shop
  - `1`: Shop accepts COD

---

#### `admin_to_pay` - Outstanding Balance
```sql
double(20,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Amount seller owes to platform (commission, fees)
- **Usage**: Track unpaid commissions

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for currency

**Fix**:
```sql
ALTER TABLE shops MODIFY admin_to_pay DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE shops MODIFY shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE shops MODIFY commission_percentage DECIMAL(8,2) NOT NULL DEFAULT 0.00;
```

---

#### `commission_percentage` - Platform Commission Rate
```sql
double(8,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Platform commission rate for this shop (overrides global setting)
- **Range**: 0.00 to 100.00 (percentage)
- **Example**: `10.00` = 10% commission

**Usage**:
```php
$platformCommission = $orderTotal * ($shop->commission_percentage / 100);
```

---

###  **Shipping Settings**

#### `shipping_cost` - Default Shipping Cost
```sql
double(20,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Default flat shipping rate for shop products
- **Usage**: Applied if product doesn't have custom shipping

---

#### `pick_up_point_id` - Associated Pickup Points
```sql
text DEFAULT NULL
```
- **Purpose**: Comma-separated IDs of pickup locations for this shop
- **Format**: `"1,5,8"`

**Better Design**: Should be a pivot table `shop_pickup_points`

---

#### `delivery_pickup_latitude`, `delivery_pickup_longitude` - Shop Location
```sql
delivery_pickup_latitude FLOAT(17,15) DEFAULT NULL
delivery_pickup_longitude FLOAT(17,15) DEFAULT NULL
```
- **Purpose**: GPS coordinates for shop location
- **Usage**:
  - Calculate distance-based shipping
  - Show shop on map
  - Enable local pickup

---

### **Bank Account Information**

```sql
bank_name VARCHAR(255) DEFAULT NULL
bank_acc_name VARCHAR(200) DEFAULT NULL
bank_acc_no VARCHAR(50) DEFAULT NULL
bank_routing_no INT(50) DEFAULT NULL
bank_payment_status INT(11) NOT NULL DEFAULT 0
```

**Purpose**: Seller's payout information

**🔴 SECURITY ISSUE**: Sensitive financial data stored as plaintext

**Recommended Fix**: Encrypt sensitive fields
```php
use Illuminate\Support\Facades\Crypt;

$shop->bank_acc_no = Crypt::encryptString($bankAccountNumber);
```

---

### **SEO & Meta**

#### `meta_title` - SEO Title
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Custom `<title>` tag for shop page

---

#### `meta_description` - SEO Description
```sql
text DEFAULT NULL
```
- **Purpose**: Meta description for search engines

---

### **Timestamps**

#### `updated_at`
```sql
timestamp NULL DEFAULT current_timestamp()
```

**⚠️ Missing**: No `created_at` column

**Fix**:
```sql
ALTER TABLE shops ADD COLUMN created_at TIMESTAMP NULL DEFAULT current_timestamp() AFTER bank_payment_status;
```

---

## Relationships

### Belongs To User (Seller)
```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

### Has Many Products
```php
public function products()
{
    return $this->hasMany(Product::class, 'user_id', 'user_id');
}
```

### Has Many Orders
```php
public function orders()
{
    return $this->hasMany(Order::class, 'seller_id', 'user_id');
}
```

### Belongs To Seller Package
```php
public function sellerPackage()
{
    return $this->belongsTo(SellerPackage::class);
}
```

---

## Common Queries

### Get Verified Active Shops
```php
$shops = Shop::where('verification_status', 1)
    ->where('registration_approval', 1)
    ->with('user')
    ->get();
```

### Get Top Rated Shops
```php
$topShops = Shop::where('num_of_reviews', '>', 10)
    ->orderBy('rating', 'desc')
    ->take(10)
    ->get();
```

### Get Shops with Expired Packages
```php
$expiredShops = Shop::whereNotNull('package_invalid_at')
    ->where('package_invalid_at', '<', today())
    ->get();
```

---

## Business Logic

### Shop Creation (Seller Registration)
```php
$shop = Shop::create([
    'user_id' => $seller->id,
    'name' => $request->shop_name,
    'slug' => Str::slug($request->shop_name),
    'verification_status' => 0,
    'registration_approval' => 0,
    'seller_package_id' => 1, // Free package
    'product_upload_limit' => 10,
]);
```

### Package Upgrade
```php
public function upgradePackage(SellerPackage $package)
{
    $this->seller_package_id = $package->id;
    $this->product_upload_limit = $package->product_limit;
    $this->package_invalid_at = now()->addDays($package->duration_days);
    $this->save();
}
```

---

## Security Considerations

### 1. Encrypt Sensitive Financial Data
```php
// Accessors/Mutators in Shop model
public function setBankAccNoAttribute($value)
{
    $this->attributes['bank_acc_no'] = Crypt::encryptString($value);
}

public function getBankAccNoAttribute($value)
{
    return $value ? Crypt::decryptString($value) : null;
}
```

### 2. Validate Shop Ownership
```php
public function update(Request $request, Shop $shop)
{
    if ($shop->user_id != auth()->id() && !auth()->user()->hasRole('Admin')) {
        abort(403);
    }
    
    // Proceed with update
}
```

### 3. Audit Commission Changes
```php
// Log changes to commission_percentage
if ($shop->isDirty('commission_percentage')) {
    Log::warning('Shop commission changed', [
        'shop_id' => $shop->id,
        'old' => $shop->getOriginal('commission_percentage'),
        'new' => $shop->commission_percentage,
        'changed_by' => auth()->id(),
    ]);
}
```

---

## Refactoring Opportunities

### 1. Fix Data Types
```sql
ALTER TABLE shops MODIFY rating DECIMAL(3,2) NOT NULL DEFAULT 0.00;
ALTER TABLE shops MODIFY admin_to_pay DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE shops MODIFY shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE shops MODIFY commission_percentage DECIMAL(8,2) NOT NULL DEFAULT 0.00;
ALTER TABLE shops ADD COLUMN created_at TIMESTAMP NULL DEFAULT current_timestamp();
```

### 2. Add Constraints
```sql
ALTER TABLE shops ADD CONSTRAINT fk_shop_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE shops ADD UNIQUE KEY unique_shop_user (user_id);
ALTER TABLE shops ADD UNIQUE KEY unique_shop_slug (slug);
```

### 3. Normalize Banner Data
**Current**: Multiple banner columns (bloated schema)

**Proposed**: `shop_banners` table
```sql
CREATE TABLE shop_banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_id INT NOT NULL,
    section VARCHAR(50) NOT NULL, -- 'top', 'full_1', 'half', 'full_2'
    image_id INT NOT NULL,
    link VARCHAR(500) NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);
```

### 4. Extract Bank Info to Separate Table
```sql
CREATE TABLE shop_bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_id INT NOT NULL UNIQUE,
    bank_name VARCHAR(255),
    account_holder VARCHAR(200),
    account_number_encrypted TEXT,
    routing_number_encrypted TEXT,
    payment_status TINYINT DEFAULT 0,
    verified_at TIMESTAMP NULL,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);
```

### 5. Add Indexes
```sql
CREATE INDEX idx_verification ON shops(verification_status, registration_approval);
CREATE INDEX idx_package ON shops(seller_package_id);
CREATE INDEX idx_rating ON shops(rating) WHERE verification_status = 1;
CREATE INDEX idx_slug ON shops(slug);
```

---

## Related Documentation

- [Sellers Table](./sellers_table_reference.md)
- [Users Table](./users_table_reference.md)
- [Products Table](./products_table_reference.md)
- [Orders Table](./orders_table_reference.md)
- [Seller Packages Table](./seller_packages_table_reference.md)
