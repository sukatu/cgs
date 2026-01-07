# Quick Start Guide - CGS Website

## ⚠️ IMPORTANT: PHP Server Required

PHP code will NOT work if you open HTML files directly in your browser. You MUST use a PHP server.

## Start the Website (3 Easy Steps)

### Step 1: Open Terminal
Press `Cmd + Space`, type "Terminal", press Enter

### Step 2: Navigate to Project
```bash
cd /Users/issasukatuabdullahi/Desktop/cgs
```

### Step 3: Start PHP Server
```bash
php -S localhost:8000
```

You should see:
```
PHP 8.x.x Development Server started at http://localhost:8000
```

## Access Your Website

Open your browser and go to:
```
http://localhost:8000/index.html
```

## Setup Database (One-Time)

1. Make sure MySQL is running
2. Visit: `http://localhost:8000/database_setup.php` (creates admin tables)
3. Visit: `http://localhost:8000/database_setup_users.php` (creates user tables)

## Admin Login

- URL: `http://localhost:8000/admin-login.php`
- Username: `admin`
- Password: `cgs2025`

## Stop the Server

Press `Ctrl + C` in the terminal

---

## Alternative: Use the Start Script

```bash
cd /Users/issasukatuabdullahi/Desktop/cgs
./start-server.sh
```

That's it! The server will start automatically.

