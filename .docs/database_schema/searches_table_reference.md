# Searches Table - Complete Column Reference

**Database**: `ecommerce`  
**Table**: `searches`  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_unicode_ci

---

## Overview

The `searches` table tracks search queries performed by users on the frontend. This data is used for "Trending Searches"
and to help administrators understand what customers are looking for.

---

## Column Breakdown

| Column         | Type            | Nullable | Key | Default               | Extra            | Description                                             |
|:---------------|:----------------|:---------|:----|:----------------------|:-----------------|:--------------------------------------------------------|
| **id**         | `int(11)`       | No       | PRI | *NULL*                | `auto_increment` | Internal unique identifier for the search query record. |
| **query**      | `varchar(1000)` | No       |     | *NULL*                |                  | The actual search term entered by the user.             |
| **count**      | `int(11)`       | No       |     | `1`                   |                  | Number of times this exact query has been searched.     |
| **created_at** | `timestamp`     | No       |     | `current_timestamp()` |                  | Timestamp when this query was first recorded.           |
| **updated_at** | `timestamp`     | No       |     | `current_timestamp()` |                  | Timestamp of the most recent search for this term.      |

---

## Common Queries

### Most Popular Searches

```sql
SELECT query, count
FROM searches
ORDER BY count DESC
LIMIT 10;
```

### Recent Trending Searches

```sql
SELECT query
FROM searches
WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY count DESC;
```

---

## Notes & Observations

- **Normalization**: The application checks if a query already exists; if so, it increments the `count` and updates
  `updated_at` instead of creating a new row.
- **SQL Injection**: Since this stores user input, ensures the query is sanitized before being saved.
- **Privacy**: No `user_id` is linked here, making this data anonymized and aggregated.
