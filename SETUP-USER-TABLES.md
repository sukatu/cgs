# User Dashboard Tables Setup

This guide explains how to create the necessary database tables for the user dashboard functionality.

## Required Tables

The user dashboard requires the following tables:

1. **user_papers** - Stores papers submitted by users
2. **user_library** - Stores bookmarked articles and videos
3. **users** - Must have `profile_picture` and `bio` columns

## Setup Options

### Option 1: Complete Setup (Recommended for New Installations)

Run the complete database setup script that creates ALL tables:

```
http://localhost/cgs/create-all-tables.php
```

This will create:
- events
- admin_users
- users (with profile_picture column)
- event_registrations
- user_papers
- user_library

### Option 2: User Tables Only (If Other Tables Already Exist)

If you already have the main tables (events, users, etc.) and only need to add the user dashboard tables:

```
http://localhost/cgs/create-user-tables.php
```

This will:
- Create `user_papers` table
- Create `user_library` table
- Add `profile_picture` column to `users` table (if missing)
- Add `bio` column to `users` table (if missing)
- Add foreign key constraints
- Add unique constraints

## Table Structures

### user_papers
- Stores user-submitted papers
- Fields: id, user_id, title, abstract, keywords, category, file_path, status, submitted_date, reviewed_date
- Foreign key to users table

### user_library
- Stores bookmarked articles and videos
- Fields: id, user_id, title, description, resource_url, resource_type, resource_id, saved_date
- Unique constraint on (user_id, resource_type, resource_id) to prevent duplicates
- Foreign key to users table

### users (additional columns)
- `profile_picture` VARCHAR(500) - Path to user's profile picture
- `bio` TEXT - User's biography

## Verification

After running the setup script, verify the tables exist:

1. Check your database using phpMyAdmin or MySQL command line
2. Or visit the user dashboard: `http://localhost/cgs/user-dashboard.php`
3. Try submitting a paper or bookmarking an article

## Troubleshooting

### Foreign Key Errors
If you see foreign key constraint errors:
- Make sure the `users` table exists first
- Run `database_setup_users.php` if needed
- The scripts will attempt to create tables without foreign keys if constraints fail

### Column Already Exists
If you see "column already exists" errors:
- This is normal - the script checks if columns exist before adding them
- The tables are safe to run multiple times

### Table Already Exists
If tables already exist:
- The scripts use `CREATE TABLE IF NOT EXISTS`, so they're safe to run multiple times
- Only missing tables/columns will be created

## Manual SQL Setup

If you prefer to run SQL manually, use the SQL file:

```sql
-- See database-schema-user-tables.sql
```

Run the SQL commands in that file using phpMyAdmin or MySQL command line.
