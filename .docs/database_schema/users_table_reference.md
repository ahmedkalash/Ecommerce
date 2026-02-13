# Users Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `users`  
**Engine**: InnoDB  
**Charset**: utf8  
**Collation**: utf8_unicode_ci

---

## Overview

The `users` table is the **central identity table** for all actors in the multi-vendor e-commerce platform. It
implements a **Single Table Inheritance (STI)** pattern where different user types (Admin, Customer, Seller, Delivery
Boy, Staff) are distinguished by the `user_type` column rather than separate tables.

**⚠️ Known Issue**: This is a "God Table" anti-pattern where one table handles authentication, authorization, profile
data, social login, wallet balance, and more. See [Refactoring Opportunities](#refactoring-opportunities) below.

---

## Column Specifications

### **Core Identity Columns**

#### `id` - Primary Key

```sql
int(9) unsigned auto_increment primary key
```

- **Purpose**: Unique identifier for each user
- **Type**: Auto-incrementing unsigned integer
- **Max Value**: 999,999,999 (9 digits)
- **Usage**: Referenced as foreign key in ~30+ tables (orders, products, carts, reviews, etc.)
- **Security Issue**: Sequential IDs are predictable, enabling IDOR attacks

**Used In**:

- Foreign key in: `orders`, `carts`, `products`, `wishlists`, `addresses`, `reviews`, `transactions`, etc.
- API responses (should use UUID for public identifiers)

---

#### `user_type` - Role Discriminator

```sql
enum('admin', 'staff', 'customer', 'seller', 'delivery_boy') default 'customer' not null
```

- **Purpose**: Determines the user's role in the system (STI discriminator)
- **Allowed Values**:
    - `admin`: System administrator with full access
    - `customer`: Regular buyer
    - `seller`: Vendor who owns a shop
    - `delivery_boy`: Fulfillment staff
    - `staff`: Administrative staff member

**Business Logic**:

```php
// Route protection
if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
```

**⚠️ Critical Bug**: `staff` user type is not in the ENUM definition but is used throughout the codebase. This causes
database constraint violations.

**Related Tables**:

- `customers`: Extended data for `user_type='customer'` (Note: This table might be deprecated/merged)
- `sellers`: Extended data for `user_type='seller'`

---

#### `name` - Display Name

```sql
varchar(255) not null
```

- **Purpose**: User's full name or display name
- **Validation**: Required during registration
- **Usage**: Shown in UI, emails, invoices, reviews
- **Default**: For social login users without a name, set to `'Apple User'` or provider name

---

#### `email` - Email Address

```sql
varchar(255) null, unique (users_email_unique)
```

- **Purpose**: Primary login credential and communication channel
- **Nullable**: Yes (allows phone-only registration when OTP addon is active)
- **Unique Constraint**: Prevents duplicate email registrations
- **Usage**:
    - Login credential (alternative to phone)
    - Password reset
    - Email notifications
    - Social login identifier

**Business Rules**:

- Can be NULL if user registers with phone only
- Must be unique if provided
- Verified via `email_verified_at` timestamp

---

#### `password` - Hashed Password

```sql
varchar(255) null
```

- **Purpose**: Bcrypt-hashed password for authentication
- **Nullable**: Yes (social login users may not have a password initially)
- **Hash Algorithm**: Bcrypt (via `Hash::make()`)
- **Length**: 60 characters (bcrypt output), but column allows 255 for future algorithms

**Security**:

```php
// Creation
Hash::make($request->password)

// Verification
Hash::check($request->password, $user->password)
```

---

### **Authentication & Verification**

#### `email_verified_at` - Email Verification Timestamp

```sql
timestamp null
```

- **Purpose**: Marks when the user's email was verified
- **NULL**: Email not yet verified
- **Non-NULL**: Email is verified, value is the verification timestamp

**Verification Methods**:

1. **Auto-verified**: Set immediately if `email_verification` setting is disabled
2. **Post-registration**: Set after user clicks email verification link
3. **Pre-registration**: Set during registration if user verified before signing up

**Usage**:

```php
// Check if verified
if ($user->email_verified_at === null) {
    return redirect('/email/verify');
}
```

**Middleware**: `verified` middleware checks this column

---

#### `verification_code` - Multi-Purpose Verification Code

```sql
text null
```

- **Purpose**: **OVERLOADED** - Used for THREE different verification scenarios
- **Type**: TEXT (variable length) to accommodate both encrypted strings and numeric codes

**Three Use Cases**:

1. **Email Verification (Laravel UI)**:
   ```php
   $verification_code = encrypt($user->id); // Returns encrypted string
   ```

2. **Password Reset**:
   ```php
   $user->verification_code = rand(100000, 999999); // 6-digit code
   ```

3. **Phone/SMS Verification (OTP System)**:
   ```php
   $user->verification_code = rand(100000, 999999); // 6-digit code
   ```

**⚠️ Problem**:

- One column serves three purposes
- No expiration logic
- Codes can collide (password reset overwrites email verification)
- 6-digit codes are vulnerable to brute force

**Recommended Refactoring**:

```sql
email_verification_token varchar(255) null
password_reset_code varchar(6) null
phone_verification_code varchar(6) null
verification_expires_at timestamp null
```

---

#### `new_email_verificiation_code` - Email Change Verification

```sql
text null
```

- **Purpose**: Temporary verification code when user requests to change their email
- **Type**: Encrypted string (similar to `verification_code` for email verification)

**Flow**:

1. User requests email change to `new@example.com`
2. System generates code: `encrypt($user->id)`
3. Sends email to `new@example.com` with callback link
4. User clicks link → Email updated, code cleared

**Route**: `GET /email-change/callback?new_email_verificiation_code={code}`

**⚠️ Typo**: Column name has spelling error: `verificiation` should be `verification`

---

#### `remember_token` - Laravel Remember Me Token

```sql
varchar(100) null
```

- **Purpose**: Laravel's "Remember Me" functionality for persistent login
- **Auto-Managed**: Laravel automatically generates and updates this
- **Length**: 60 characters (Laravel default)

**Usage**: Automatically handled by `AuthenticatesUsers` trait when user checks "Remember Me"

---

### **Social Login Columns**

#### `provider` - OAuth Provider Name

```sql
varchar(255) null
```

- **Purpose**: Identifies which social login provider the user used
- **Allowed Values**: `'google'`, `'facebook'`, `'twitter'`, `'apple'`
- **NULL**: User registered via standard email/password

**Usage**:

```php
if ($user->provider) {
    // User signed up via social login
}
```

---

#### `provider_id` - OAuth User ID

```sql
varchar(50) null
```

- **Purpose**: Unique user ID from the OAuth provider
- **Example**: Google user ID, Facebook user ID
- **Uniqueness**: Should be unique per provider (not enforced in DB)

**Usage**:

```php
// Check if user exists by social ID
User::where('provider', 'google')
    ->where('provider_id', $googleUserId)
    ->first();
```

---

#### `access_token` - OAuth Access Token

```sql
longtext null
```

- **Purpose**: Stores OAuth access token for API calls to the provider
- **Type**: LONGTEXT (can be very long for some providers)
- **Security**: Should be encrypted at rest (currently stored in plaintext)

**Usage**: Can be used to revoke social login access or make API calls to provider

---

#### `refresh_token` - OAuth Refresh Token

```sql
text null
```

- **Purpose**: Token to refresh the access token when it expires
- **Providers**: Google, Apple (Facebook tokens don't expire)

**⚠️ Security Issue**: Storing long-lived tokens in plaintext is a security risk

---

### **Session & Device**

#### `device_token` - Push Notification Token

```sql
varchar(255) null
```

- **Purpose**: FCM/APNS token for push notifications to mobile devices
- **Updated**: When user logs in via mobile app
- **Usage**: Send push notifications for order updates, messages, etc.

---

### **Profile Data**

#### `avatar` - Avatar File ID

```sql
varchar(256) null
```

- **Purpose**: Foreign key to `uploads` table (file ID)
- **Type**: File ID (integer stored as string)

**Usage**:

```php
$user->uploads()->where('id', $user->avatar)->first();
```

---

#### `avatar_original` - Direct Avatar URL

```sql
varchar(256) null
```

- **Purpose**: Direct URL to avatar image (often from social login)
- **Usage**: For social login users, stores the provider's avatar URL
- **Example**: `https://lh3.googleusercontent.com/a/...`

**Confusion**: Project uses both `avatar` (file ID) and `avatar_original` (URL) inconsistently

---

#### `address` - Street Address

```sql
varchar(300) null
```

- **Purpose**: User's default street address
- **Usage**: Auto-fill during checkout
- **⚠️ Issue**: Should be in a separate `addresses` table (and it is, creating duplication)

---

#### `country`, `state`, `city` - Location Fields

```sql
country varchar(30) null
state   varchar(30) null
city    varchar(30) null
```

- **Purpose**: User's location for shipping defaults
- **Type**: Varchar (should reference `countries`, `states`, `cities` tables instead)
- **⚠️ Issue**: Duplicates data from `addresses` table

---

#### `postal_code` - Postal/ZIP Code

```sql
varchar(20) null
```

- **Purpose**: Postal code for shipping
- **Length**: 20 chars to accommodate international formats

---

#### `phone` - Phone Number

```sql
varchar(20) null
```

- **Purpose**: Contact phone number, also used as login credential
- **Format**: Stored with country code, e.g., `+201234567890`
- **Usage**:
    - Login credential (alternative to email)
    - OTP verification
    - Order notifications

**Business Rules**:

- Can be NULL if user registers with email only
- Not enforced as unique (allows duplicate phone numbers)

---

### **Financial & Wallet**

#### `balance` - Wallet Balance

```sql
double(20,2) default 0.00 not null
```

- **Purpose**: User's internal wallet balance for purchases
- **Currency**: System default currency
- **Operations**: Recharge, refund, purchase deduction

**🔴 CRITICAL ISSUE**: Using `DOUBLE` for financial data causes precision errors

**Problem**:

```php
0.1 + 0.2 = 0.30000000000000004 (in floating point)
```

**Required Fix**:

```sql
ALTER TABLE users MODIFY balance DECIMAL(15,2) DEFAULT 0.00 NOT NULL;
```

**Usage**:

```php
// Deduct from wallet
$user->balance -= $order->total;
$user->save();
```

---

### **Moderation & Security**

#### `banned` - Ban Status

```sql
tinyint default 0 not null
```

- **Purpose**: Marks if user is banned from the system
- **Values**:
    - `0`: Active user
    - `1`: Banned user

**Middleware**: `unbanned` middleware blocks banned users

**Usage**:

```php
if ($user->banned == 1) {
    abort(403, 'Your account has been banned');
}
```

---

#### `is_suspicious` - Fraud Flag

```sql
tinyint default 0 null
```

- **Purpose**: Flags potentially fraudulent accounts
- **Values**:
    - `0`: Normal
    - `1`: Suspicious activity detected

**Usage**: Manual review by admins, potentially auto-detected by fraud rules

---

### **Referral & Packages**

#### `referred_by` - Referrer User ID

```sql
int null
```

- **Purpose**: ID of the user who referred this user (for affiliate/referral system)
- **Addon**: Requires `affiliate_system` addon
- **Usage**: Track referral chains, calculate commissions

**Not a Foreign Key**: Should reference `users.id` but not enforced

---

#### `referral_code` - User's Unique Referral Code

```sql
varchar(255) null
```

- **Purpose**: Unique code this user can share to refer others
- **Format**: Auto-generated alphanumeric string
- **Usage**: Stored in cookie when visitor clicks referral link

**Example**: `ABC123XYZ`

---

#### `customer_package_id` - Subscription Package

```sql
int null
```

- **Purpose**: ID of the customer package/membership tier
- **Foreign Key**: References `customer_packages` table (not enforced)
- **Usage**: Determines upload limits, features access

---

#### `remaining_uploads` - Upload Quota

```sql
int default 0 null
```

- **Purpose**: Number of product uploads remaining for customer packages
- **Reset**: Based on package renewal
- **Decremented**: Each time customer uploads a product

---

### **Timestamps**

#### `created_at` - Registration Timestamp

```sql
timestamp null
```

- **Purpose**: When the user account was created
- **Auto-Managed**: Laravel automatically sets this

---

#### `updated_at` - Last Modification

```sql
timestamp null
```

- **Purpose**: When the user record was last updated
- **Auto-Managed**: Laravel automatically updates this on any change

---

## Indexes & Constraints

### Primary Key

```sql
PRIMARY KEY (id)
```

### Unique Constraints

```sql
CONSTRAINT users_email_unique UNIQUE (email)
```

- Ensures no duplicate email addresses
- Allows NULL emails (phone-only registration)

### Missing Indexes (Performance Issue)

The following columns are frequently used in WHERE clauses but lack indexes:

- `user_type` (used in almost every query)
- `phone` (login credential)
- `provider` + `provider_id` (composite index for social login)
- `referral_code` (referral lookups)
- `banned` (access control)

**Recommended**:

```sql
CREATE INDEX idx_user_type ON users(user_type);
CREATE INDEX idx_phone ON users(phone);
CREATE INDEX idx_provider ON users(provider, provider_id);
CREATE INDEX idx_referral_code ON users(referral_code);
```

---

## Refactoring Opportunities

### 1. Fix `user_type` ENUM

**Current**:

```sql
enum('admin', 'staff', 'customer', 'seller', 'delivery_boy')
```

**Status**:  
✅ **Fixed**: The `staff` type is now included, and `Seller` has been corrected to lowercase `seller`.

### 2. Split Verification Codes

**Current**: One `verification_code` column for multiple purposes

**Recommended**:

```sql
ALTER TABLE users 
ADD COLUMN email_verification_token VARCHAR(255) NULL,
ADD COLUMN password_reset_code VARCHAR(6) NULL,
ADD COLUMN phone_verification_code VARCHAR(6) NULL,
ADD COLUMN verification_expires_at TIMESTAMP NULL;
```

### 3. Fix Financial Column

```sql
ALTER TABLE users 
MODIFY balance DECIMAL(15,2) DEFAULT 0.00 NOT NULL;
```

### 4. Move Profile Data

**Problem**: Address, phone, etc. duplicated between `users` and `addresses` tables

**Solution**: Remove `address`, `country`, `state`, `city`, `postal_code` from `users` table. Use `addresses` table
exclusively.

### 5. Add Foreign Key Constraints

```sql
ALTER TABLE users 
ADD CONSTRAINT fk_referred_by 
FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE users 
ADD CONSTRAINT fk_customer_package 
FOREIGN KEY (customer_package_id) REFERENCES customer_packages(id) ON DELETE SET NULL;
```

---

## Security Recommendations

1. **Use UUIDs for Public IDs**: Add `public_id` UUID column for API exposure
2. **Encrypt Sensitive Tokens**: Encrypt `access_token`, `refresh_token`, `device_token`
3. **Add Rate Limiting**: Implement verification code attempt limits
4. **Expire Verification Codes**: Add `verification_expires_at` column
5. **Hash Device Tokens**: Don't store FCM tokens in plaintext
6. **Audit Trail**: Add `last_login_at`, `last_login_ip` for security monitoring

---

## Related Documentation

- [Auth Module Analysis](../Auth_Module_Analysis.md)
- [Customer Registration Verification](../business_settings/customer_registration_verify.md)
- [Authentication Flow Diagrams](../Auth_Module_Analysis.md#code-flow-analysis)
