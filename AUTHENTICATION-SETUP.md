# Authentication System Setup

This document explains how the user authentication system is connected to the backend.

## Authentication Flow

### 1. Login Pages

There are two login entry points:

- **`login-user.html`** - Standalone login page
  - Form posts to: `user-auth.php?action=login`
  - Supports redirect parameter: `?redirect=page.php`
  - Shows error messages from session
  - Redirects to dashboard if already logged in

- **`login.php`** - Alternative login page
  - Also posts to: `user-auth.php?action=login`
  - Same functionality as login-user.html

- **`network.php`** - Network page with login/registration
  - Login form posts to: `user-auth.php?action=login`
  - Registration form posts to: `user-auth.php?action=register`
  - Toggle between login and registration forms

### 2. Backend Authentication Handler

**File: `user-auth.php`**

Handles three actions:

#### Login (`action=login`)
- Validates email and password
- Queries `users` table
- Verifies password using `password_verify()`
- Sets session variables:
  - `$_SESSION['user_logged_in'] = true`
  - `$_SESSION['user_id'] = user_id`
  - `$_SESSION['user_name'] = user_name`
  - `$_SESSION['user_email'] = user_email`
- Redirects to dashboard or specified redirect URL
- Shows error message on failure

#### Registration (`action=register`)
- Validates all required fields
- Checks password strength (minimum 6 characters)
- Verifies passwords match
- Checks if email already exists
- Creates new user with hashed password
- Auto-logs in after successful registration
- Redirects to user dashboard

#### Logout (`logout=1`)
- Clears all session variables
- Destroys session cookie
- Destroys session
- Redirects to home page

### 3. Protected Pages

Pages that require authentication use:

```php
require_once 'config.php';
requireUserLogin();
```

The `requireUserLogin()` function:
- Checks if `$_SESSION['user_logged_in']` is set and true
- Redirects to `login-user.html` if not logged in
- Preserves redirect URL for return after login

### 4. Session Management

**File: `config.php`**

Contains helper functions:
- `isUserLoggedIn()` - Check if user is logged in
- `requireUserLogin()` - Require login, redirect if not
- `getDBConnection()` - Database connection

Session is started automatically in `config.php`:
```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### 5. Security Features

✅ **Password Hashing**
- Uses PHP's `password_hash()` with bcrypt
- Passwords are never stored in plain text
- Uses `password_verify()` for authentication

✅ **SQL Injection Prevention**
- All queries use prepared statements
- Parameters are bound with `bind_param()`

✅ **Session Security**
- Session variables are set only after successful authentication
- Session is destroyed on logout
- Redirect URLs are sanitized

✅ **Input Validation**
- Email format validation
- Password strength requirements
- Required field checks
- Email uniqueness check

## Usage Examples

### Login from any page:
```html
<a href="login-user.html">Login</a>
```

### Login with redirect:
```html
<a href="login-user.html?redirect=user-dashboard.php">Login</a>
```

### Logout:
```html
<a href="user-auth.php?logout=1">Logout</a>
```

### Check if user is logged in (PHP):
```php
<?php
require_once 'config.php';
if (isUserLoggedIn()) {
    echo "Welcome, " . $_SESSION['user_name'];
}
?>
```

### Protect a page:
```php
<?php
require_once 'config.php';
requireUserLogin();
// Page content here - user is guaranteed to be logged in
?>
```

## Database Requirements

The authentication system requires:

1. **`users` table** with columns:
   - `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
   - `email` (VARCHAR, UNIQUE)
   - `password_hash` (VARCHAR)
   - `name` (VARCHAR)
   - Other profile fields (optional)

2. **Session support** in PHP configuration

## Testing

1. **Test Login:**
   - Go to `login-user.html`
   - Enter valid credentials
   - Should redirect to dashboard

2. **Test Registration:**
   - Go to `network.php`
   - Fill registration form
   - Should create account and auto-login

3. **Test Logout:**
   - While logged in, click logout
   - Should clear session and redirect to home

4. **Test Protected Pages:**
   - Try accessing `user-dashboard.php` without login
   - Should redirect to login page
   - After login, should return to dashboard

## Troubleshooting

### "Invalid email or password" error
- Check if user exists in database
- Verify password hash is correct
- Check database connection

### Session not persisting
- Check PHP session configuration
- Verify session_start() is called
- Check browser cookies are enabled

### Redirect not working
- Verify redirect URL is relative (not external)
- Check for output before header() calls
- Ensure no whitespace before <?php tag

### "Already logged in" redirect loop
- Clear browser cookies
- Check session variables are set correctly
- Verify logout functionality works
