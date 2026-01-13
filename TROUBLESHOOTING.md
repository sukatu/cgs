# Troubleshooting Blank Pages After Login/Registration

## Problem
After successful login or registration, users see a blank page instead of being redirected to the dashboard.

## Common Causes

### 1. Output Before Headers
**Symptom:** "Headers already sent" error in logs
**Solution:** 
- All PHP files now use output buffering (`ob_start()`)
- All redirects use `ob_end_clean()` before `header('Location: ...')`
- Check for whitespace or echo statements before headers

### 2. Session Issues
**Symptom:** Session not starting properly
**Solution:**
- Ensure `session_start()` is called before any output
- Check session save path permissions on cPanel
- Verify session cookies are being set

### 3. PHP Errors
**Symptom:** Fatal errors causing blank page
**Solution:**
- Check error logs in cPanel (Error Log section)
- Enable error logging: `ini_set('log_errors', 1);`
- Check PHP version compatibility (requires PHP 7.0+)

### 4. File Path Issues
**Symptom:** Files not found
**Solution:**
- Ensure all files are uploaded to the correct directory
- Check file permissions (644 for files, 755 for directories)
- Verify `config.php` has correct database credentials

### 5. Database Connection
**Symptom:** Database errors
**Solution:**
- Verify database credentials in `config.php`
- Ensure database tables exist (run `create-all-tables.php`)
- Check database user permissions

## Debugging Steps

### Step 1: Test Redirects
1. Access `test-redirect.php` in your browser
2. If redirect works, you'll see "Redirect Success!" page
3. If it fails, check error logs

### Step 2: Check Error Logs
1. In cPanel, go to "Error Log" section
2. Look for PHP errors related to:
   - `user-auth.php`
   - `user-dashboard.php`
   - Database connection errors
   - "Headers already sent" errors

### Step 3: Test Database Connection
1. Access `debug-auth.php` in your browser
2. Check if database connection works
3. Verify users table exists

### Step 4: Check File Permissions
```bash
# Files should be 644
chmod 644 *.php *.html

# Directories should be 755
chmod 755 uploads/
chmod 755 images/
```

### Step 5: Enable Error Display (Temporary)
Add to top of `user-auth.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

**⚠️ Remove this after debugging for security!**

## Files Modified

1. **user-auth.php**
   - Added output buffering at start
   - Added `ob_end_clean()` before all redirects
   - Improved error logging

2. **user-dashboard.php**
   - Added output buffering at start
   - Ensured session starts before output

3. **config.php**
   - Already has session start logic
   - No changes needed

## Quick Fix Checklist

- [ ] All files uploaded to cPanel
- [ ] Database credentials correct in `config.php`
- [ ] Database tables created (`create-all-tables.php`)
- [ ] File permissions set correctly
- [ ] Error logs checked
- [ ] PHP version is 7.0 or higher
- [ ] Session save path is writable

## Still Having Issues?

1. Check cPanel error logs
2. Run `debug-auth.php` to see detailed diagnostics
3. Test `test-redirect.php` to verify redirect functionality
4. Contact hosting support if database connection fails

## Security Note

After debugging, ensure:
- `debug-auth.php` is deleted or protected
- `test-redirect.php` is deleted or protected
- Error display is disabled (`ini_set('display_errors', 0)`)
- Error logging is enabled (`ini_set('log_errors', 1)`)
