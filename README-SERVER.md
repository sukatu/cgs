# How to Run the CGS Website with PHP

## Problem
If you're seeing PHP code as text instead of it executing, you need to run the site through a PHP server.

## Solution: Use PHP Built-in Server

### Option 1: Command Line (Recommended)

1. Open Terminal
2. Navigate to the project folder:
   ```bash
   cd /Users/issasukatuabdullahi/Desktop/cgs
   ```

3. Start PHP server:
   ```bash
   php -S localhost:8000
   ```

4. Open your browser and go to:
   ```
   http://localhost:8000/index.html
   ```

5. To stop the server, press `Ctrl+C` in the terminal

### Option 2: Use the Start Script

1. Make the script executable:
   ```bash
   chmod +x start-server.sh
   ```

2. Run it:
   ```bash
   ./start-server.sh
   ```

3. Open browser: `http://localhost:8000`

### Option 3: Use XAMPP/MAMP (if installed)

1. Place the `cgs` folder in:
   - XAMPP: `htdocs/cgs/`
   - MAMP: `htdocs/cgs/`

2. Access via:
   - XAMPP: `http://localhost/cgs/`
   - MAMP: `http://localhost:8888/cgs/`

## Important Files That Need PHP Server

These files contain PHP code and must be accessed through a server:
- `login.html` (has PHP code)
- `network.php` (converted from HTML)
- `admin-login.php`
- `admin-dashboard.php`
- `user-dashboard.php`
- `api-events.php`
- All files in `/admin/` folder

## Testing PHP is Working

1. Start the PHP server
2. Visit: `http://localhost:8000/api-events.php`
3. You should see JSON output, not PHP code

## Troubleshooting

**Error: "php: command not found"**
- Install PHP: `brew install php` (on Mac)
- Or use XAMPP/MAMP which includes PHP

**Still seeing PHP code?**
- Make sure you're accessing via `http://localhost:8000` not `file://`
- Check that PHP server is running
- Verify file permissions

