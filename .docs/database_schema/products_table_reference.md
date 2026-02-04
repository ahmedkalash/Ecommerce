# Products Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `products`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `products` table is the **central catalog table** for all products in the multi-vendor e-commerce platform. It supports:
- Simple and variant products
- Digital and physical products
- Seller and admin-added products
- Featured products, flash deals, and auctions
- Wholesale pricing
- SEO optimization

**Complexity**: This is one of the most complex tables in the database with **60+ columns**, reflecting the diverse product types and features supported.

**Related Tables**:
- `product_stocks`: Variant-specific inventory (SKU, price, quantity)
- `product_taxes`: Tax configurations per product
- `categories`: Product categorization
- `brands`: Product brands
- `users`: Product owner (seller or admin)
- `cart`, `wishlist`, `orders`: Shopping and purchasing
- `reviews`: Product ratings and comments

---

## Column Specifications

### **Primary Key**

#### `id` - Product ID
```sql
int(11) NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for each product
- **Usage**: Referenced in orders, carts, wishlists, reviews, etc.

---

### **Core Product Information**

#### `name` - Product Name
```sql
varchar(200) NOT NULL
```
- **Purpose**: Display name of the product
- **Max Length**: 200 characters
- **Usage**: Shown in listings, product pages, cart, orders

**Example**: "Samsung Galaxy S24 Ultra 5G (256GB, Phantom Black)"

**Multilingual Support**: Translations stored in `product_translations` table

---

#### `added_by` - Product Source
```sql
varchar(6) NOT NULL DEFAULT 'admin'
```
- **Purpose**: Indicates who added the product
- **Values**:
  - `'admin'`: Added by admin/staff
  - `'seller'`: Added by a vendor

**Usage**:
```php
if ($product->added_by == 'seller') {
    // Seller product - requires approval
    // Commission calculated on sale
}
```

**⚠️ Issue**: Column type is `varchar(6)` but should be ENUM for data integrity
```sql
ALTER TABLE products MODIFY added_by ENUM('admin', 'seller') NOT NULL DEFAULT 'admin';
```

---

#### `user_id` - Owner User ID
```sql
int(11) NOT NULL
```
- **Purpose**: Foreign key to `users.id` (the seller or admin who owns the product)
- **Business Logic**:
  - If `added_by = 'seller'` → user is a vendor
  - If `added_by = 'admin'` → user is admin/staff

**Usage**:
```php
$product = Product::find(1);
$product->user->name; // Owner's name
$product->user->shop->name ?? 'Admin'; // Shop name if seller
```

**⚠️ Missing Constraint**: No foreign key to `users` table

---

###  **Categorization**

#### `category_id` - Primary Category
```sql
int(11) NOT NULL
```
- **Purpose**: Foreign key to `categories.id`
- **Usage**: Main product category for filtering and navigation

**Note**: Products can belong to multiple categories via `product_categories` pivot table.

---

#### `brand_id` - Product Brand
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Foreign key to `brands.id`
- **Nullable**: Yes (some products may not have a brand)

---

### **Media Assets**

#### `photos` - Product Images
```sql
varchar(2000) DEFAULT NULL
```
- **Purpose**: Comma-separated list of upload IDs or file paths
- **Format**: `"123,456,789"` (references `uploads` table)

**Usage**:
```php
$photoIds = explode(',', $product->photos);
foreach ($photoIds as $photoId) {
    $image = Upload::find($photoId);
    echo "<img src='{$image->file_name}'>";
}
```

---

#### `thumbnail_img` - Main Thumbnail
```sql
varchar(100) DEFAULT NULL
```
- **Purpose**: Upload ID for the primary product image
- **Usage**: Shown in product listings, cart, mobile app

---

#### `short_video` - Short Video File
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Upload ID for product short video (TikTok/Instagram Reels style)
- **Usage**: Featured on product page for engagement

---

#### `short_video_thumbnail` - Video Preview Image
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Thumbnail image shown before video plays

---

#### `video_provider` - Video Hosting Service
```sql
varchar(20) DEFAULT NULL
```
- **Values**: `'youtube'`, `'vimeo'`, `'dailymotion'`
- **Usage**: Embed product video from external platform

---

#### `video_link` - External Video URL
```sql
longtext DEFAULT NULL
```
- **Purpose**: Full URL to external video
- **Example**: `https://www.youtube.com/watch?v=VIDEO_ID`

---

### **Pricing & Discounts**

#### `unit_price` - Base Price
```sql
double(20,2) NOT NULL
```
- **Purpose**: Regular selling price per unit
- **Required**: Yes

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for financial data causes precision errors

**Fix**:
```sql
ALTER TABLE products MODIFY unit_price DECIMAL(20,2) NOT NULL;
ALTER TABLE products MODIFY purchase_price DECIMAL(20,2) NULL;
ALTER TABLE products MODIFY discount DECIMAL(20,2) NOT NULL DEFAULT 0.00;
```

---

#### `purchase_price` - Cost Price
```sql
double(20,2) DEFAULT NULL
```
- **Purpose**: Seller's cost/purchase price (for profit calculation)
- **Visibility**: Admin/seller only (not shown to customers)

---

#### `discount` - Discount Amount
```sql
double(20,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Discount value
- **Type Depends On**: `discount_type`

---

#### `discount_type` - Discount Method
```sql
varchar(10) NOT NULL DEFAULT 'amount'
```
- **Values**:
  - `'amount'`: Fixed amount discount (e.g., $10 off)
  - `'percent'`: Percentage discount (e.g., 20% off)

**Price Calculation**:
```php
$finalPrice = $product->unit_price;

if ($product->discount > 0) {
    if ($product->discount_type == 'amount') {
        $finalPrice = $product->unit_price - $product->discount;
    } else { // percent
        $finalPrice = $product->unit_price - ($product->unit_price * $product->discount / 100);
    }
}
```

---

#### `discount_start_date`, `discount_end_date` - Discount Schedule
```sql
discount_start_date int(11) DEFAULT NULL
discount_end_date   int(11) DEFAULT NULL
```
- **Purpose**: Unix timestamp for scheduled discount
- **Usage**: Auto-activate/deactivate discount

**Check if Discount Active**:
```php
public function hasActiveDiscount()
{
    $now = time();
    
    if ($this->discount <= 0) {
        return false;
    }
    
    if ($this->discount_start_date && $now < $this->discount_start_date) {
        return false; // Not started yet
    }
    
    if ($this->discount_end_date && $now > $this->discount_end_date) {
        return false; // Expired
    }
    
    return true;
}
```

---

### **Variants & Options**

#### `variant_product` - Has Variants Flag
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Simple product (single SKU)
  - `1`: Variant product (multiple SKUs in `product_stocks`)

---

#### `attributes` - Product Attributes
```sql
varchar(1000) NOT NULL DEFAULT '[]'
```
- **Purpose**: JSON array of attribute IDs
- **Format**: `[1, 3, 5]` (references `attributes` table)
- **Example**: Size, Color, Material

---

#### `choice_options` - User-Selectable Options
```sql
mediumtext DEFAULT NULL
```
- **Purpose**: JSON structure defining variant choices
- **Example**:
```json
[
  {
    "attribute_id": "1",
    "values": ["S", "M", "L", "XL"]
  },
  {
    "attribute_id": "3",
    "values": ["Red", "Blue", "Black"]
  }
]
```

---

#### `colors` - Available Colors
```sql
mediumtext DEFAULT NULL
```
- **Purpose**: JSON array of color codes
- **Format**: `["#FF0000", "#0000FF", "#000000"]`

---

#### `variations` - Variant Combinations
```sql
text DEFAULT NULL
```
- **Purpose**: JSON defining all possible variant combinations with prices
- **Example**:
```json
[
  {
    "type": "S-Red",
    "price": 29.99,
    "sku": "SHIRT-S-RED"
  },
  {
    "type": "M-Blue",
    "price": 31.99,
    "sku": "SHIRT-M-BLUE"
  }
]
```

**Note**: Actual inventory managed in `product_stocks` table.

---

### **Stock & Inventory**

#### `current_stock` - Total Available Quantity
```sql
int(10) NOT NULL DEFAULT 0
```
- **Purpose**: Total units available across all variants
- **Decremented**: On order placement
- **Incremented**: On product restock or order cancellation

**⚠️ Data Integrity**: Must match sum of `product_stocks.qty` for variant products

---

#### `low_stock_quantity` - Stock Alert Threshold
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Trigger low stock alert when `current_stock` falls below this
- **Usage**: Admin/seller notification system

---

#### `stock_visibility_state` - Stock Display Mode
```sql
varchar(10) NOT NULL DEFAULT 'quantity'
```
- **Values**:
  - `'quantity'`: Show exact stock ("23 in stock")
  - `'text'`: Show text only ("In Stock" / "Out of Stock")
  - `'hide'`: Don't show stock status

---

#### `unit` - Measurement Unit
```sql
varchar(20) DEFAULT NULL
```
- **Purpose**: Unit of measurement
- **Examples**: `'pc'`, `'kg'`, `'liter'`, `'box'`, `'meter'`

---

#### `min_qty` - Minimum Order Quantity
```sql
int(11) NOT NULL DEFAULT 1
```
- **Purpose**: Minimum units customer must purchase
- **Usage**: Wholesale products often have `min_qty > 1`

**Validation**:
```php
if ($quantity < $product->min_qty) {
    return back()->withErrors("Minimum order quantity is {$product->min_qty}");
}
```

---

### **Product Status Flags**

#### `published` - Visibility Status
```sql
int(11) NOT NULL DEFAULT 1
```
- **Values**:
  - `0`: Draft/unpublished (not visible to customers)
  - `1`: Published (visible on site)

---

#### `approved` - Admin Approval
```sql
tinyint(1) NOT NULL DEFAULT 1
```
- **Values**:
  - `0`: Pending approval (seller products)
  - `1`: Approved

**Workflow**:
```php
if ($product->added_by == 'seller' && $product->approved == 0) {
    // Product awaiting admin approval
}
```

---

#### `featured` - Platform Featured
```sql
int(11) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Regular product
  - `1`: Featured by admin (shown in featured sections)

---

#### `seller_featured` - Seller Promoted
```sql
int(11) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Regular
  - `1`: Seller-promoted (highlighted in seller's shop)

---

#### `todays_deal` - Today's Deal Flag
```sql
int(11) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Not in today's deal
  - `1`: Featured in today's deal section

---

### **Shipping**

#### `shipping_type` - Shipping Method
```sql
varchar(20) DEFAULT 'flat_rate'
```
- **Values**:
  - `'free'`: Free shipping
  - `'flat_rate'`: Fixed shipping cost
  - `'product_wise'`: Calculated per product

---

#### `shipping_cost` - Base Shipping Fee
```sql
double(20,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Shipping cost for this product

---

#### `is_quantity_multiplied` - Multiply Shipping by Qty
```sql
tinyint(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Shipping cost is flat regardless of quantity
  - `1`: Shipping cost multiplied by quantity

**Calculation**:
```php
$shippingCost = $product->shipping_cost;
if ($product->is_quantity_multiplied == 1) {
    $shippingCost *= $quantity;
}
```

---

#### `est_shipping_days` - Estimated Delivery Time
```sql
int(11) DEFAULT NULL
```
- **Purpose**: Estimated delivery days
- **Usage**: Display "Delivery in 3-5 days"

---

#### `weight` - Product Weight
```sql
double(8,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Product weight for shipping calculation
- **Unit**: Typically in kg or lbs (defined in settings)

---

### **Tax Configuration**

#### `tax` - Default Tax Amount
```sql
double(20,2) DEFAULT NULL
```
- **Purpose**: Tax rate or amount
- **Type**: Defined by `tax_type`

---

#### `tax_type` - Tax Calculation Method
```sql
varchar(10) DEFAULT NULL
```
- **Values**:
  - `'percent'`: Percentage tax
  - `'amount'`: Fixed tax amount

**Note**: Complex tax rules stored in `product_taxes` table.

---

### **Performance Metrics**

#### `num_of_sale` - Total Sales Count
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Tracks number of times product was sold
- **Usage**: "Bestseller" badges, popularity sorting

---

#### `rating` - Average Rating
```sql
double(8,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Average customer rating
- **Range**: 0.00 to 5.00

**🔴 Issue**: Should use `DECIMAL` not `DOUBLE`

---

#### `cash_on_delivery` - COD Availability
```sql
tinyint(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: COD not available
  - `1`: COD allowed for this product

---

### **Product Types**

#### `digital` - Digital Product Flag
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Physical product
  - `1`: Digital product (e-book, software, etc.)

---

#### `auction_product` - Auction Listing
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Regular product
  - `1`: Auction product (requires auction addon)

---

#### `wholesale_product` - Wholesale Flag
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Retail
  - `1`: Wholesale product (bulk pricing)

---

### **Digital Product Files**

#### `file_name` - Digital File Name
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Name of downloadable file (for digital products)

---

#### `file_path` - Digital File Location
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Path to downloadable file

---

### **External Products**

#### `external_link` - Affiliate/External URL
```sql
varchar(500) DEFAULT NULL
```
- **Purpose**: External product URL (for affiliate products)

---

#### `external_link_btn` - CTA Button Text
```sql
varchar(255) DEFAULT 'Buy Now'
```
- **Purpose**: Text for external link button
- **Example**: "Buy on Amazon", "View Details"

---

### **SEO & Marketing**

#### `slug` - URL Slug
```sql
mediumtext NOT NULL
```
- **Purpose**: SEO-friendly URL identifier
- **Example**: `samsung-galaxy-s24-ultra-5g-256gb`
- **Unique**: Should be unique (not enforced in DB)

---

#### `meta_title` - SEO Title
```sql
mediumtext DEFAULT NULL
```
- **Purpose**: HTML `<title>` tag content

---

#### `meta_description` - SEO Description
```sql
longtext DEFAULT NULL
```
- **Purpose**: Meta description for search engines

---

#### `meta_img` - Social Media Image
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: OG image for social sharing

---

#### `tags` - Search Tags
```sql
varchar(500) DEFAULT NULL
```
- **Purpose**: Comma-separated keywords for search
- **Example**: `"smartphone, android, 5g, samsung"`

---

### **Additional Features**

#### `barcode` - Product Barcode
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: UPC/EAN barcode for inventory management

---

#### `pdf` - Product PDF/Manual
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Upload ID for product brochure or manual

---

#### `description` - Product Description
```sql
longtext DEFAULT NULL
```
- **Purpose**: Full HTML product description

---

#### `frequently_bought_selection_type` - Recommendation Mode
```sql
varchar(19) DEFAULT 'product'
```
- **Values**:
  - `'product'`: Manually selected products
  - `'category'`: Auto-suggest from same category

---

#### `has_warranty` - Warranty Flag
```sql
tinyint(4) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: No warranty
  - `1`: Has warranty

---

#### `warranty_id`, `warranty_note_id` - Warranty References
```sql
warranty_id INT DEFAULT NULL
warranty_note_id INT DEFAULT NULL
```
- **Purpose**: Link to warranty terms and conditions

---

### **Timestamps**

#### `created_at`, `updated_at`
```sql
created_at TIMESTAMP NOT NULL DEFAULT current_timestamp()
updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp()
```
- **Auto-Managed**: Laravel handles these automatically

---

## Relationships

### Belongs To User (Owner)
```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

### Belongs To Category
```php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

### Has Many Product Stocks (Variants)
```php
public function stocks()
{
    return $this->hasMany(ProductStock::class);
}
```

### Has Many Reviews
```php
public function reviews()
{
    return $this->hasMany(Review::class);
}
```

---

## Common Queries

### Get Published, Approved Products
```php
Product::where('published', 1)
    ->where('approved', 1)
    ->where('current_stock', '>', 0)
    ->get();
```

### Get Featured Products
```php
Product::where('featured', 1)
    ->where('published', 1)
    ->limit(10)
    ->get();
```

### Get Bestsellers
```php
Product::where('num_of_sale', '>', 100)
    ->orderBy('num_of_sale', 'desc')
    ->take(20)
    ->get();
```

---

## Refactoring Opportunities

### 1. Fix Data Types
```sql
ALTER TABLE products MODIFY added_by ENUM('admin', 'seller') NOT NULL DEFAULT 'admin';
ALTER TABLE products MODIFY unit_price DECIMAL(20,2) NOT NULL;
ALTER TABLE products MODIFY purchase_price DECIMAL(20,2) NULL;
ALTER TABLE products MODIFY discount DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE products MODIFY rating DECIMAL(3,2) NOT NULL DEFAULT 0.00;
```

### 2. Add Foreign Keys
```sql
ALTER TABLE products ADD CONSTRAINT fk_product_user FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE products ADD CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id);
ALTER TABLE products ADD CONSTRAINT fk_product_brand FOREIGN KEY (brand_id) REFERENCES brands(id);
```

### 3. Add Unique Constraint on Slug
```sql
CREATE UNIQUE INDEX idx_unique_slug ON products(slug(191));
```

### 4. Add Indexes for Performance
```sql
CREATE INDEX idx_published_approved ON products(published, approved);
CREATE INDEX idx_category ON products(category_id);
CREATE INDEX idx_brand ON products(brand_id);
CREATE INDEX idx_user_added ON products(user_id, added_by);
CREATE INDEX idx_featured ON products(featured) WHERE published = 1;
```

---

## Related Documentation

- [Product Stocks Table](./product_stocks_table_reference.md)
- [Categories Table](./categories_table_reference.md)
- [Sellers Table](./sellers_table_reference.md)
- [Orders Table](./orders_table_reference.md)
