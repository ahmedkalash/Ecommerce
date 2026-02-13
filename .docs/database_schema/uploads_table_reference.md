# Uploads Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `uploads`  
**Engine**: InnoDB

---

## Overview

The `uploads` table is the central media registry for the application. It stores metadata for all uploaded files (
images, PDFs, videos) and links them to their storage location.

**Key Concept**: The system uses **Upload IDs** rather than direct file paths in most tables (e.g.,
`products.thumbnail_img` stores an ID like `123`).

**Related Tables**:

- referenced by almost every other table (`products`, `users`, `brands`, etc.).

---

## Column Specifications

### **Primary Key**

#### `id` - Upload ID

```sql
int(11) NOT NULL auto_increment primary key
```

---

### **File Metadata**

#### `file_original_name` - Original Filename

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: The name of the file as uploaded by the user.

#### `file_name` - Stored Filename

```sql
varchar(255) DEFAULT NULL
```

- **Purpose**: The actual path/filename on disk/S3.
- **Example**: `uploads/all/random_hash.jpg`.

#### `user_id` - Uploader

```sql
int(11) DEFAULT NULL
```

- **Purpose**: ID of the user who uploaded the file.

#### `file_size` - Size in Bytes

```sql
int(11) DEFAULT NULL
```

#### `extension` - File Extension

```sql
varchar(10) DEFAULT NULL
```

- **Example**: `jpg`, `png`, `pdf`.

#### `type` - MIME Type Category

```sql
varchar(15) DEFAULT NULL
```

- **Example**: `image`, `video`, `document`.

---

### **External Media**

#### `external_link` - URL

```sql
varchar(500) DEFAULT NULL
```

- **Purpose**: If the file is hosted externally (not on local storage/S3).

---

### **Timestamps**

#### `created_at`, `updated_at`

```sql
timestamp NOT NULL DEFAULT current_timestamp()
```

#### `deleted_at` - Soft Delete

```sql
timestamp NULL DEFAULT NULL
```

- **Purpose**: Supports soft deletion of files.
