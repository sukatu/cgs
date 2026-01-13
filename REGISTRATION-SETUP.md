# Registration System Setup

This document explains how the user registration system is connected to backend user management.

## Registration Flow

### 1. Registration Entry Points

**Primary Registration Page: `network.php`**
- Contains both login and registration forms
- Toggle between login and registration sections
- Form posts to: `user-auth.php?action=register`
- Displays error messages from session
- Client-side validation before submission

### 2. Registration Form Fields

The registration form collects:

**Required Fields:**
- Full Name
- Email Address
- Country
- City
- Profession (dropdown selection) *
- Password *
- Confirm Password *

**Optional Fields:**
- Phone Number
- Organization/Institution
- Areas of Interest (multiple checkboxes)
- Short Bio
- LinkedIn URL

**Areas of Interest Options:**
- Board Effectiveness
- Compliance
- ESG
- Risk Governance
- Digital Governance
- State-Owned Entities

**Profession Options:**
- Lawyer
- Banker
- Board Member/Director
- Student
- Regulator
- Consultant
- Academic/Researcher
- Executive/CEO
- Compliance Officer
- Auditor
- Other

### 3. Backend Registration Handler

**File: `user-auth.php` - Registration Action**

#### Validation Steps:

1. **Field Validation:**
   - Checks all required fields are filled
   - Validates email format using `filter_var()`
   - Validates password length (minimum 6 characters)
   - Verifies passwords match
   - Validates LinkedIn URL format (if provided)

2. **Email Uniqueness Check:**
   - Queries database to check if email already exists
   - Prevents duplicate registrations
   - Shows error if email is already registered

3. **Data Sanitization:**
   - Trims whitespace from all text inputs
   - Handles interests array (checkboxes)
   - Converts interests array to comma-separated string

4. **Password Security:**
   - Hashes password using `password_hash()` with bcrypt
   - Never stores plain text passwords

5. **User Creation:**
   - Inserts user into `users` table with all provided data
   - Stores: name, email, password_hash, phone, country, city, organization, role (mapped from profession field), interests, bio, linkedin_url
   - Returns new user ID on success

6. **Auto-Login:**
   - Automatically logs in user after successful registration
   - Sets session variables:
     - `$_SESSION['user_logged_in'] = true`
     - `$_SESSION['user_id'] = new_user_id`
     - `$_SESSION['user_name'] = name`
     - `$_SESSION['user_email'] = email`
   - Sets success message
   - Redirects to user dashboard

### 4. Error Handling

**Validation Errors:**
- Field-specific error messages
- Redirects back to registration form
- Preserves form data (can be enhanced)
- Displays error in red alert box

**Database Errors:**
- Catches SQL errors
- Shows user-friendly error message
- Logs technical error details (for debugging)

**Common Error Messages:**
- "Full name is required"
- "Email address is required"
- "Please enter a valid email address"
- "Password must be at least 6 characters long"
- "Passwords do not match"
- "Country is required"
- "City is required"
- "Please select your profession"
- "This email is already registered. Please login instead."
- "Registration failed. Please try again."

### 5. Database Schema

**Users Table Required Columns:**
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR)
- email (VARCHAR, UNIQUE)
- password_hash (VARCHAR)
- phone (VARCHAR, optional)
- country (VARCHAR)
- city (VARCHAR)
- organization (VARCHAR, optional)
- role (VARCHAR) - stored as "profession" in UI but kept as "role" in database for compatibility
- interests (TEXT, optional) - comma-separated
- bio (TEXT, optional)
- linkedin_url (VARCHAR, optional)
- profile_picture (VARCHAR, optional)
- email_verified (BOOLEAN, default 0)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### 6. Security Features

✅ **Password Security:**
- Passwords hashed with bcrypt
- Minimum 6 characters required
- Password confirmation required

✅ **Input Validation:**
- Email format validation
- URL format validation (LinkedIn)
- Required field validation
- Data sanitization (trim whitespace)

✅ **SQL Injection Prevention:**
- All queries use prepared statements
- Parameters bound with `bind_param()`
- No direct string concatenation in SQL

✅ **Email Uniqueness:**
- Prevents duplicate accounts
- Checks before insertion

✅ **Session Management:**
- Secure session handling
- Auto-login after registration
- Session variables set correctly

### 7. User Management Integration

After registration, users can:

1. **Access Dashboard:**
   - View profile information
   - Update account details
   - Upload profile picture
   - Change password

2. **Submit Papers:**
   - Upload research papers
   - Track submission status
   - View submitted papers

3. **Manage Library:**
   - Bookmark articles
   - Bookmark videos
   - View all saved content

4. **Register for Events:**
   - Book event registrations
   - View registration history

### 8. Testing Registration

**Test Successful Registration:**
1. Go to `network.php`
2. Click "Create Account" or toggle to registration form
3. Fill all required fields
4. Submit form
5. Should create account, auto-login, and redirect to dashboard

**Test Validation:**
1. Try submitting with empty required fields
2. Try invalid email format
3. Try mismatched passwords
4. Try password less than 6 characters
5. Try registering with existing email
6. All should show appropriate error messages

**Test Optional Fields:**
1. Register with only required fields
2. Register with all fields including optional ones
3. Both should work correctly

### 9. Troubleshooting

**"Registration failed" error:**
- Check database connection
- Verify users table exists
- Check table structure matches expected schema
- Review PHP error logs

**Email already exists but user can't login:**
- Check if email is in database
- Verify password hash format
- Check login handler

**Interests not saving:**
- Verify interests field exists in database
- Check how interests array is handled
- Ensure proper string conversion

**Auto-login not working:**
- Check session is started
- Verify session variables are set
- Check redirect after registration

## Integration Points

### With User Dashboard:
- Registration creates user account
- User can immediately access dashboard
- Profile information pre-filled from registration

### With Authentication:
- Registration uses same authentication system
- Auto-login uses same session management
- Logout works for registered users

### With Database:
- Creates record in `users` table
- All fields properly stored
- Foreign key relationships maintained
