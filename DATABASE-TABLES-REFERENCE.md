# CGS Database Tables – New & Updated Reference

This document lists **newly created** and **updated** tables used by the CGS application (event registration, admin gallery, etc.). Use it to create or verify tables on your server.

---

## Newly created tables (from recent work)

### 1. `online_zoom_registrations`

Used for **online (Zoom) guest registrations** from the CGS II registration page. Created automatically by `register-online.php` on first use, or run the SQL below once.

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK AUTO_INCREMENT | Primary key |
| event_id | INT(11) NULL | Event ID (e.g. 999 for CGS II) |
| event_title | VARCHAR(255) NULL | Event title |
| full_name | VARCHAR(255) NOT NULL | Registrant name |
| email | VARCHAR(255) NOT NULL | Email address |
| phone | VARCHAR(50) NULL | Phone (optional) |
| registration_date | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | When they registered |
| status | ENUM('pending','confirmed','cancelled') DEFAULT 'pending' | Status |
| email_sent | TINYINT(1) DEFAULT 0 | 1 if Zoom link email was sent |
| zoom_link_sent_at | TIMESTAMP NULL | When the email was sent |
| notes | TEXT NULL | Admin notes |

**SQL to create manually (if needed):**

```sql
CREATE TABLE IF NOT EXISTS online_zoom_registrations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    event_id INT(11) DEFAULT NULL,
    event_title VARCHAR(255) DEFAULT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    email_sent TINYINT(1) DEFAULT 0,
    zoom_link_sent_at TIMESTAMP NULL,
    notes TEXT,
    INDEX idx_event (event_id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_registration_date (registration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 2. `in_person_registrations`

Used for **in-person event registrations** (CGS II and others). Created automatically by `register-inperson.php` on first use, or by `create-inperson-registrations-table.php`.

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK AUTO_INCREMENT | Primary key |
| event_id | INT(11) NULL | Event ID |
| event_title | VARCHAR(255) NULL | Event title |
| full_name | VARCHAR(255) NOT NULL | Full name |
| email | VARCHAR(255) NOT NULL | Email |
| phone | VARCHAR(50) NOT NULL | Phone |
| address | TEXT NOT NULL | Address |
| institution_firm | VARCHAR(255) NOT NULL | Institution/Firm |
| registration_date | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | When they registered |
| status | ENUM('pending','confirmed','cancelled') DEFAULT 'pending' | Status |
| notes | TEXT NULL | Admin notes |

**SQL to create manually (if needed):**

```sql
CREATE TABLE IF NOT EXISTS in_person_registrations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    event_id INT(11) DEFAULT NULL,
    event_title VARCHAR(255) DEFAULT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    institution_firm VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    INDEX idx_event (event_id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_registration_date (registration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Auto-created / updated in app (admin dashboard)

### 3. `gallery_images`

Used by the **admin dashboard** for the gallery. If the table does not exist when the dashboard loads, it is created automatically. You can also create it manually with the SQL below.

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK AUTO_INCREMENT | Primary key |
| filename | VARCHAR(255) NOT NULL | Stored filename |
| original_filename | VARCHAR(255) NULL | Original upload name |
| alt_text | VARCHAR(500) NULL | Alt text |
| category | VARCHAR(100) NULL | Category |
| display_order | INT(11) DEFAULT 0 | Sort order |
| is_active | BOOLEAN DEFAULT 1 | 1 = show, 0 = hide |
| upload_date | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Upload time |

**SQL to create manually (if needed):**

```sql
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255),
    alt_text VARCHAR(500),
    category VARCHAR(100),
    display_order INT(11) DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Existing tables (unchanged schema, used by registration/admin)

- **`event_registrations`** – User (logged-in) event bookings; used in “Online Registrations” in admin (user bookings).
- **`events`** – Event definitions.
- **`users`** – Front-end user accounts.
- **`admin_users`** – Admin logins.

---

## Summary

| Table | New? | Created / updated by |
|-------|------|----------------------|
| **online_zoom_registrations** | **Yes (new)** | `register-online.php` (auto) or run SQL above |
| **in_person_registrations** | **Yes (new)** | `register-inperson.php` (auto) or `create-inperson-registrations-table.php` |
| **gallery_images** | Updated/auto-created | `admin-dashboard.php` (auto if missing) or `create_images_table.php` |
| event_registrations | No | Already existed (user bookings) |

On a **fresh server**, you can run the three `CREATE TABLE IF NOT EXISTS` blocks above (in order) to ensure all registration and gallery tables exist, or rely on the app to create `online_zoom_registrations` and `in_person_registrations` on first registration and `gallery_images` on first admin gallery load.
