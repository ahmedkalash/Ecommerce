# Base Build Data Directory

## Purpose

This directory contains **essential seed data** required for the application to function correctly. These files are
imported during database migrations to populate tables with configuration, settings, and reference data.

---

## Attention

- All these files will be imported using a migration not seeder.
- All these files will be imported in the testing environment also for now to avoid any errors in the testing
  environment because of the tide coupling in the code and non clear responsibility for each module.

---

## File Organization

Files are numbered to ensure correct import order. The numbering system:

- `0_xxx` - Initialization scripts (run first)
- `1-99_xxx` - Data imports (run in sequence)
- `100_xxx` - Finalization scripts (run last)

---

## File Descriptions

### Core Configuration Files

#### `0_init_db_config.sql` (569 bytes)

**Purpose:** Database initialization and configuration settings.

**Contains:**

- SQL mode configuration (`NO_AUTO_VALUE_ON_ZERO`)
- Transaction initialization
- Timezone settings
- Character set configuration (utf8mb4)

**Required for:**

- ✅ Testing environment
- ✅ Development environment
- ✅ Production environment

**When to skip:** Never - Always needed

---

#### `2_business_settings_table.sql` (17 KB)

**Purpose:** Core application configuration and business settings.

**Contains:**

- Table: `business_settings`
- Application settings (site name, currency, timezone, etc.)
- Feature toggles (OTP, social login, reCAPTCHA,...etc)
- Payment gateway configurations
- Email/SMS service settings

**Required for:**

- ✅ Testing environment - **CRITICAL** (app won't run without it)
- ✅ Development environment
- ✅ Production environment

**When to skip:** Never - This is the most critical file

---

#### `100_final_db_config.sql` (204 bytes)

**Purpose:** Database finalization and cleanup.

**Contains:**

- `COMMIT;` statement to finalize transaction
- Character set restoration
- Cleanup commands

**Required for:**

- ✅ Testing environment
- ✅ Development environment
- ✅ Production environment

**When to skip:** Never - Ensures data integrity

---

### Translation & Localization Files

#### `1_app_translations_table.sql` (218 KB, 2,022 rows)

**Purpose:** **LEGACY** UI translation system (deprecated, kept for backwards compatibility).

**Contains:**

- Table: `app_translations`
- 2,022 translation entries
- Keys with `_ucf` suffix (e.g., `all_category_ucf`)
- **Limited to 255 characters** per translation
- No indexes (slow performance)
- utf8 charset (3-byte, no emoji support)

**Required for:** backwards compatibility

It is a Legacy Table: The app_translations table is deprecated in favor of the translations table. Unless you are
specifically writing tests for legacy backward-compatibility logic, this table is not needed.
Recommendation

If you are writing a test that specifically requires translation data:
Preferred: Use the newer translations table (which supports emojis and longer text), effectively mocking the data using
a Factory or creating a few specific rows in your test's setUp() method rather than importing the entire 3MB SQL dump.
Legacy: If you absolutely must test existing legacy code that queries app_translations, create just the few specific
rows you need within that specific test case.

**Schema:** `int(11)` ID, nullable columns, no performance optimization

---

#### `7_translations_table.sql` (3.1 MB, 27,083 rows)

**Purpose:** **MODERN** multi-language translation system (active, preferred).

**Contains:**

- Table: `translations`
- **27,083 translation entries** (13× more than `app_translations`)
- Clean keys without suffix (e.g., `all_category`)
- **Unlimited text length** (`longtext` field)
- **Two indexes** on `lang` and `lang_key` (fast lookups)
- utf8mb4 charset (4-byte, supports emojis 🎉)

**Required for:**

- ❌ Testing environment: only needed if you are explicitly testing:
    - The Translation System Itself: Verifying that the app correctly switches languages (e.g., en -> fr).
    - UI Text Assertions: If your test checks for the presence of specific text like ->assertSee('Add to Cart') instead
      of ->assertSee('add_to_cart').
- ✅ Development environment - For multi-language content
- ✅ Production environment

**When to skip:** Testing (unless specifically testing internationalization)

**Schema:** `bigint unsigned` ID, NOT NULL constraints, indexed for performance

**Migration Note:** This is the newer system. `app_translations` data was migrated and expanded here (from 2K→27K rows
with multi-language support).

---

### Product & Catalog Data

#### `3.multiple_tables.sql` (19 KB)

**Purpose:** Demo product catalog data.

**Contains tables (6 tables total):**

1. `attributes` - Product attributes (Size, Color, etc.)
2. `blogs`
3. `blog_categories`
4. `brands` - Brand definitions (23 brands like Nike, Audi, etc.)
5. `brand_translations` - Multi-language brand names
6. `categories` - Product categories (47 categories)

**Required for:**

- ✅ Testing environment
- ✅ Development environment - For product and blogs system feature development
- ✅ Production environment - Only if using demo data

**When to skip:** Testing (unless specifically testing product/catalog features)

---

### Location & Shipping Data

#### `4_cities_table.sql` (4.5 MB)

**Purpose:** Worldwide city database for shipping/location features.

**Contains:**

- Table: `cities`
- > 100,000 city records worldwide
- City-to-state/country mappings
- Used for address selection in checkout

**Required for:**

- ❌ Testing environment - Too large, NOT NEEDED
- ⚠️ Development environment - Optional (create test cities instead)
- ✅ Production environment - Only if using location-based shipping

**When to skip:** Testing (always), Development (unless testing location features)

**Performance Impact:** 4.5MB takes ~5-10 seconds to import

**Alternative for dev/test:** Create 5-10 test cities manually with a seeder

---

#### `5_states_table.sql` (334 KB)

**Purpose:** State/province database for location selection.

**Contains:**

- Table: `states`
- State/province records for multiple countries
- State-to-country mappings

**Required for:**

- ❌ Testing environment - NOT NEEDED
- ⚠️ Development environment - Optional
- ✅ Production environment - If using location-based features

**When to skip:** Testing (always), Development (unless testing location features)

---

### UI & Frontend Elements

#### `6.multiple_tables.sql` (486 KB)

**Purpose:** UI elements, colors, alerts, and frontend configurations.

**Contains tables (multiple):**

1. `colors` - Color definitions for product variants
2. `newsletters` - Email subscription form data
3. `custom_alerts` - Banner alert configurations
4. Other UI-related configuration tables

**Required for:**

- ❌ Testing environment - "Skip for Testing".
- ✅ Development environment - For frontend feature development
- ✅ Production environment

**When to skip:** Testing (unless testing specific UI features)

---

### User Data

#### `9_users_table.sql` (3.2 KB, 6 demo users)

**Purpose:** Demo user accounts for development.

**Contains:**

- Table: `users`
- 4 demo seller accounts
- 1 demo customer account
- 1 admin account (Ahmed)

**Required for:**

- ❌ Testing environment - NOT NEEDED (create users in factories)
- ✅ Development environment - Convenient for manual testing
- ❌ Production environment - **NEVER import demo users to production!**

**When to skip:** Testing (always), Production (always)

**Security Note:** Demo users have weak passwords. Only for local development!

#### `8_uploads_table.sql` (33 KB, 165 rows)

**Purpose:** File metadata for demo images/assets.

**Contains:**

- Table: `uploads`
- Maps database IDs to file paths (e.g., `uploads/all/image.png`)
- mostly demo product images and UI banners

**Required for:**

- ❌ Testing environment - NOT NEEDED
- ✅ Development environment - If using demo products (`3.multiple_tables.sql`)
- ❌ Production environment - NOT NEEDED

**When to skip:** Testing, Production (unless setting up a demo site)

---

## Import Strategies

### Strategy 1: Minimal Import (For Testing) ⚡

**Files to import:**

- `0_init_db_config.sql` (569 bytes)
- `2_business_settings_table.sql` (17 KB)
- `100_final_db_config.sql` (204 bytes)

**Total size:** ~18 KB  
**Import time:** ~0.5 seconds  
**Use case:** PHPUnit tests, especially authentication tests

**Coverage:** 100% database schema, minimal essential data

---

### Strategy 2: Development Import (For Local Dev) 🔧

**Files to import:**

- `0_init_db_config.sql`
- `1_app_translations_table.sql` (skip - use modern `translations` instead)
- `2_business_settings_table.sql`
- `3.multiple_tables.sql` (if developing product features)
- `4_cities_table.sql`
- `5_states_table.sql`
- `6.multiple_tables.sql`
- `7_translations_table.sql` (recommended for multi-language)
- `9_users_table.sql` (convenient demo users)
- `100_final_db_config.sql`

**Total size:** ~4-8 MB (depending on selections)  
**Import time:** ~5-15 seconds  
**Use case:** Local development with realistic data

---

### Strategy 3: Full Import (For Staging/Production Setup) 🚀

**Files to import:** All files in numerical order

**Total size:** ~8.6 MB  
**Import time:** ~15-20 seconds  
**Use case:** Initial staging/production database setup

**Warning:** Review demo data before importing to production!

---

## How to Import

### Automatic Import (Recommended)

The migration file `database/migrations/0000_00_00_000001_import_base_data.php`
automatically imports the correct files based on environment:

```bash
# Testing environment (minimal import)
APP_ENV=testing php artisan migrate:fresh

# Development environment (selective import)
php artisan migrate

# Production (never run migrate:fresh!)
php artisan migrate
```

---

### Adding New Reference Data

If adding new required reference data:

1. Create new numbered file (e.g., `10_new_data.sql`)
2. Update this README with file description
3. Update migration import logic if needed
4. Document which environments need it

---

## Performance Benchmarks

Based on MariaDB 10.4 with default settings:

| File        | Size  | Rows   | Import Time | Skip for Tests? |
|-------------|-------|--------|-------------|-----------------|
| 0_init      | 569B  | -      | ~10ms       | ❌ Never         |
| 1_app_trans | 218KB | 2,022  | ~2s         | ✅ Yes (legacy)  |
| 2_settings  | 17KB  | 82     | ~0.3s       | ❌ Never         |
| 3_catalog   | 19KB  | varies | ~0.3s       | ✅ Yes           |
| 4_cities    | 4.5MB | >100K  | ~8s         | ✅ Always        |
| 5_states    | 334KB | varies | ~2s         | ✅ Yes           |
| 6_ui        | 486KB | varies | ~2s         | ✅ Yes           |
| 7_trans     | 3.1MB | 27,083 | ~5s         | ✅ Yes           |
| 8_uploads   | 32KB  | 165    | ~0.5s       | ✅ Yes           |
| 9_users     | 3.2KB | 6      | ~0.1s       | ✅ Yes           |
| 100_final   | 204B  | -      | ~10ms       | ❌ Never         |

**Total (all files):** ~8.6MB, ~20s  
**Minimal (testing):** ~18KB, ~0.5s ⚡

---

## Troubleshooting

### Issue: "Duplicate entry" errors

**Cause:** File imported twice without TRUNCATE

**Solution:** Ensure each data file has `TRUNCATE TABLE` before `INSERT`

---

### Issue: Slow test execution

**Cause:** Importing too much data (especially `4_cities.sql`)

**Solution:** Use minimal import strategy for testing

---

### Issue: Foreign key constraint errors

**Cause:** Files imported in wrong order

**Solution:** Follow numerical order, ensure `0_init` runs first

---

## Version History

- **2026-01-29:** Initial organization, split from monolithic dump
- **Future:** Will transition to Laravel seeders for better version control

---

## Related Files

- [../schema/base_schema.sql] - Complete database schema (~110 tables)
- [0000_00_00_000000_create_base_schema.php](../migrations/0000_00_00_000000_create_base_schema.php) - Complete database
  schema (~110 tables)
- [../migrations/0000_00_00_000001_import_base_data.php] - Migration that imports these files

---

**Last Updated:** 2026-01-29 21:30 UTC+2  
**Questions?** Refer to this documentation or ask.

---

## Translation Tables Deep Dive

See the detailed comparison above (lines 78-112) for why there are TWO translation tables and which one to use.
