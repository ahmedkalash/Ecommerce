# Categories Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `categories`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `categories` table implements a **hierarchical category structure** for product classification using the **adjacency list pattern** (self-referencing parent/child relationships).

**Key Features**:
- Multi-level nested categories (parent/child/grandchild)
- Category-specific commission rates
- Scheduled category discounts
- Featured and top categories
- Digital product categories
- SEO optimization per category
- Refund policy per category

**Related Tables**:
- `category_translations`: Multilingual category names
- `products`: Products belonging to categories
- `product_categories`: Many-to-many pivot for products with multiple categories

---

## Column Specifications

### **Primary Key**

#### `id` - Category ID
```sql
int(11) NOT NULL auto_increment primary key
```
- **Purpose**: Unique identifier for each category
- **Usage**: Referenced in products, filters, breadcrumbs

---

### **Hierarchy Structure**

#### `parent_id` - Parent Category
```sql
int(11) DEFAULT 0
```
- **Purpose**: Self-referencing foreign key to `categories.id`
- **Values**:
  - `0`: Root/top-level category
  - `>0`: Child category (references parent)

**Example Hierarchy**:
```
Electronics (parent_id = 0) [Root]
├─ Smartphones (parent_id = 1)
│  ├─ Android Phones (parent_id = 2)
│  └─ iPhones (parent_id = 2)
└─ Laptops (parent_id = 1)
   ├─ Gaming Laptops (parent_id = 5)
   └─ Business Laptops (parent_id = 5)
```

**Retrieve Children**:
```php
$category = Category::find(1);
$children = Category::where('parent_id', $category->id)->get();
```

**Retrieve All Descendants (Recursive)**:
```php
public function allDescendants()
{
    return $this->children()->with('allDescendants');
}
```

**⚠️ Missing Constraint**:
```sql
ALTER TABLE categories ADD CONSTRAINT fk_category_parent 
FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL;
```

---

#### `level` - Depth Level
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Indicates category depth in hierarchy
- **Values**:
  - `0`: Root level
  - `1`: 1st level subcategory
  - `2`: 2nd level subcategory
  - etc.

**Usage**:
```php
// Get only top-level categories
$rootCategories = Category::where('level', 0)->get();

// Generate breadcrumb
$breadcrumb = [];
$current = $category;
while ($current && $current->level > 0) {
    array_unshift($breadcrumb, $current);
    $current = $current->parent;
}
```

---

### **Core Information**

#### `name` - Category Name
```sql
varchar(50) NOT NULL
```
- **Purpose**: Display name
- **Max Length**: 50 characters
- **Multilingual**: Translations in `category_translations` table

**Display**:
```blade
<h1>{{ $category->name }}</h1>
```

---

#### `slug` - URL Slug
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: SEO-friendly URL identifier
- **Example**: `smartphones`, `gaming-laptops`
- **Usage**: `/category/smartphones`

**⚠️ Should Be Unique**:
```sql
ALTER TABLE categories ADD UNIQUE KEY unique_category_slug (slug);
```

---

### **Display Settings**

#### `order_level` - Sort Order
```sql
int(11) NOT NULL DEFAULT 0
```
- **Purpose**: Manual sorting within same parent
- **Lower Value**: Displayed first
- **Usage**: Admin can drag-drop to reorder categories

**Query**:
```php
$categories = Category::where('parent_id', 0)
    ->orderBy('order_level')
    ->get();
```

---

#### `featured` - Featured Category Flag
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Regular category
  - `1`: Featured (shown in homepage, special sections)

**Usage**:
```php
$featuredCategories = Category::where('featured', 1)
    ->orderBy('order_level')
    ->take(8)
    ->get();
```

---

#### `top` - Top Category Flag
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Regular
  - `1`: Top category (shown in header/menu)

**Navigation**:
```php
$topCategories = Category::where('top', 1)
    ->orderBy('order_level')
    ->get();
```

---

#### `digital` - Digital Products Category
```sql
int(1) NOT NULL DEFAULT 0
```
- **Values**:
  - `0`: Physical products
  - `1`: Digital products (downloads, software, ebooks)

**Usage**: Filter categories by product type

---

### **Visual Assets**

#### `banner` - Category Banner Image
```sql
varchar(100) DEFAULT NULL
```
- **Purpose**: Upload ID for category page header banner
- **Display**: Top of category listing page

---

#### `icon` - Category Icon
```sql
varchar(100) DEFAULT NULL
```
- **Purpose**: Upload ID for category icon
- **Display**: Menu, category grid, breadcrumbs

---

#### `cover_image` - Category Cover
```sql
varchar(100) DEFAULT NULL
```
- **Purpose**: Upload ID for category thumbnail/cover
- **Display**: Category grid on homepage

---

### **Pricing & Discounts**

#### `commision_rate` - Seller Commission
```sql
double(8,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Platform commission rate for products in this category
- **Range**: 0.00 to 100.00 (percentage)
- **Overrides**: Global commission if set

**🔴 TYPO**: Column name misspelled as `commision_rate` (should be `commission_rate`)

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for percentages

**Fix**:
```sql
ALTER TABLE categories CHANGE commision_rate commission_rate DECIMAL(8,2) NOT NULL DEFAULT 0.00;
ALTER TABLE categories MODIFY discount DECIMAL(20,2) NOT NULL DEFAULT 0.00;
```

**Usage**:
```php
$category = Product::find(1)->category;
$commissionAmount = $orderTotal * ($category->commission_rate / 100);
```

---

#### `discount` - Category-Wide Discount
```sql
double(20,2) NOT NULL DEFAULT 0.00
```
- **Purpose**: Discount applied to all products in category
- **Type**: Percentage (e.g., 20.00 = 20% off)

---

#### `discount_start_date`, `discount_end_date` - Discount Schedule
```sql
discount_start_date INT(11) DEFAULT NULL
discount_end_date INT(11) DEFAULT NULL
```
- **Purpose**: Unix timestamps for scheduled category-wide sales
- **Usage**: Auto-activate/deactivate category discount

**Check if Active**:
```php
public function hasActiveDiscount()
{
    $now = time();
    
    if ($this->discount <= 0) {
        return false;
    }
    
    if ($this->discount_start_date && $now < $this->discount_start_date) {
        return false;
    }
    
    if ($this->discount_end_date && $now > $this->discount_end_date) {
        return false;
    }
    
    return true;
}
```

---

### **Refund Policy**

#### `refund_request_time` - Refund Window
```sql
int(10) unsigned DEFAULT NULL
```
- **Purpose**: Number of days customers can request refund
- **NULL**: No refunds for this category
- **Example**: `30` = 30-day refund window

**Usage**:
```php
if ($product->category->refund_request_time) {
    $refundDeadline = $order->delivered_date->addDays($product->category->refund_request_time);
    
    if (now() <= $refundDeadline) {
        // Allow refund request
    }
}
```

---

### **SEO Optimization**

#### `meta_title` - SEO Title
```sql
varchar(255) DEFAULT NULL
```
- **Purpose**: Custom `<title>` tag for category page
- **Falls Back To**: Category name if NULL

---

#### `meta_description` - SEO Description
```sql
text DEFAULT NULL
```
- **Purpose**: Meta description for search engines

---

### **Timestamps**

#### `created_at`, `updated_at`
```sql
created_at TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
updated_at TIMESTAMP NULL DEFAULT current_timestamp()
```

**⚠️ Issue**: `created_at` has `ON UPDATE current_timestamp()` which changes the creation timestamp on every update

**Fix**:
```sql
ALTER TABLE categories MODIFY created_at TIMESTAMP NOT NULL DEFAULT current_timestamp();
```

---

## Relationships

### Belongs To Parent
```php
public function parent()
{
    return $this->belongsTo(Category::class, 'parent_id');
}
```

### Has Many Children
```php
public function children()
{
    return $this->hasMany(Category::class, 'parent_id');
}
```

### Has Many Products
```php
public function products()
{
    return $this->hasMany(Product::class);
}
```

### Has Many Translations
```php
public function translations()
{
    return $this->hasMany(CategoryTranslation::class);
}
```

---

## Common Queries

### Get Root Categories
```php
$rootCategories = Category::where('parent_id', 0)
    ->orderBy('order_level')
    ->get();
```

### Get Category Tree (Nested)
```php
$categoryTree = Category::where('parent_id', 0)
    ->with('children.children') // 3 levels deep
    ->orderBy('order_level')
    ->get();
```

### Get Featured Categories with Products Count
```php
$featured = Category::where('featured', 1)
    ->withCount('products')
    ->orderBy('order_level')
    ->get();
```

### Get All Ancestors (Breadcrumb)
```php
public function ancestorsAndSelf()
{
    $categories = [];
    $current = $this;
    
    while ($current) {
        array_unshift($categories, $current);
        $current = $current->parent;
    }
    
    return collect($categories);
}
```

---

## Business Logic

### Category Creation with Auto Level
```php
public function createCategory($data)
{
    $level = 0;
    
    if ($data['parent_id'] > 0) {
        $parent = Category::find($data['parent_id']);
        $level = $parent->level + 1;
    }
    
    return Category::create([
        'name' => $data['name'],
        'parent_id' => $data['parent_id'] ?? 0,
        'level' => $level,
        'slug' => Str::slug($data['name']),
    ]);
}
```

### Move Category to Different Parent
```php
public function moveTo(Category $newParent)
{
    $this->parent_id = $newParent->id;
    $this->level = $newParent->level + 1;
    $this->save();
    
    // Recursively update all descendants' levels
    $this->updateDescendantsLevel();
}

private function updateDescendantsLevel()
{
    foreach ($this->children as $child) {
        $child->level = $this->level + 1;
        $child->save();
        $child->updateDescendantsLevel();
    }
}
```

### Delete Category with Children Handling
```php
public function deleteWithChildren()
{
    // Option 1: Move children to parent
    Category::where('parent_id', $this->id)
        ->update(['parent_id' => $this->parent_id]);
    
    // Option 2: Delete children recursively (CASCADE)
    foreach ($this->children as $child) {
        $child->deleteWithChildren();
    }
    
    $this->delete();
}
```

---

## Security Considerations

### 1. Prevent Circular References
**Issue**: Category could become its own ancestor

**Validation**:
```php
public function setParentId($newParentId)
{
    if ($newParentId == $this->id) {
        throw new \Exception('Category cannot be its own parent');
    }
    
    // Check if new parent is a descendant
    $descendants = $this->allDescendantIds();
    if (in_array($newParentId, $descendants)) {
        throw new \Exception('Cannot move category under its own descendant');
    }
    
    $this->parent_id = $newParentId;
}
```

### 2. Limit Hierarchy Depth
**Issue**: Too deep nesting affects performance

**Recommendation**: Maximum 4-5 levels
```php
public function createCategory($data)
{
    if ($data['parent_id'] > 0) {
        $parent = Category::find($data['parent_id']);
        if ($parent->level >= 4) {
            throw new \Exception('Maximum category depth (5 levels) exceeded');
        }
    }
    
    // Proceed with creation
}
```

---

## Performance Considerations

### 1. Add Indexes
```sql
CREATE INDEX idx_parent ON categories(parent_id);
CREATE INDEX idx_level ON categories(level);  
CREATE INDEX idx_featured ON categories(featured) WHERE featured = 1;
CREATE INDEX idx_top ON categories(top) WHERE top = 1;
CREATE INDEX idx_slug ON categories(slug);
```

### 2. Cache Category Tree
```php
// Cache for 1 hour
$categoryTree = Cache::remember('category_tree', 3600, function () {
    return Category::where('parent_id', 0)
        ->with('children.children.children')
        ->orderBy('order_level')
        ->get();
});
```

### 3. Use Nested Set Model (Alternative)
**Current**: Adjacency List (simple but slow for deep queries)

**Alternative**: Nested Set Model (via `kalnoy/nestedset` package)
- Faster for tree traversal
- More complex to maintain

---

## Refactoring Opportunities

### 1. Fix Data Types & Typo
```sql
ALTER TABLE categories CHANGE commision_rate commission_rate DECIMAL(8,2) NOT NULL DEFAULT 0.00;
ALTER TABLE categories MODIFY discount DECIMAL(20,2) NOT NULL DEFAULT 0.00;
ALTER TABLE categories MODIFY created_at TIMESTAMP NOT NULL DEFAULT current_timestamp();
```

### 2. Add Constraints
```sql
ALTER TABLE categories ADD CONSTRAINT fk_category_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE;
ALTER TABLE categories ADD UNIQUE KEY unique_category_slug (slug);
```

### 3. Add Status Column
**Current**: No way to disable a category

**Proposed**:
```sql
ALTER TABLE categories ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active';
```

### 4. Add Path Column (Materialized Path Pattern)
**Purpose**: Store full hierarchy path for faster queries

```sql
ALTER TABLE categories ADD COLUMN path VARCHAR(500) NULL;
```

**Example**: `Electronics/Smartphones/Android`

**Benefits**:
- Fast ancestor queries
- Easy breadcrumb generation
- Simplified URL generation

---

## Related Documentation

- [Category Translations Table](./category_translations_table_reference.md)
- [Products Table](./products_table_reference.md)
- [Product Categories Table](./product_categories_table_reference.md)
