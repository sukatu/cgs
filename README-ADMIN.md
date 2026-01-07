# CGS Admin Dashboard Setup Guide

## Initial Setup

1. **Create Database Tables**
   - Open your browser and navigate to: `http://localhost/cgs/database_setup.php`
   - This will create all necessary database tables
   - A default admin user will be created:
     - Username: `admin`
     - Password: `cgs2025`
   - **IMPORTANT**: Change the default password after first login!

2. **Access Admin Dashboard**
   - Navigate to: `http://localhost/cgs/admin-login.php`
   - Login with the default credentials
   - Once logged in, you can manage events and webinars

## Features

### Event Management
- **Add Events**: Click "Add New Event" button
- **Edit Events**: Click "Edit" on any event row
- **Delete Events**: Click "Delete" (with confirmation)
- **View All Events**: Table shows all events with status, dates, and types

### Event Fields
- Title (required)
- Description
- Event Type (Webinar, Series, Other)
- Date & Time (required)
- Location
- Format (Online, Hybrid, In-Person)
- Registration Link
- YouTube URL (for videos)
- Speakers (comma-separated)
- Moderator
- Agenda
- Summary
- Tags (comma-separated)
- Countries (comma-separated)
- Status (Upcoming, Completed, Cancelled)

## Security Notes

1. **Change Default Password**
   - Edit `config.php` and change `ADMIN_PASSWORD` constant
   - Or update password in database using MySQL:
     ```sql
     UPDATE admin_users 
     SET password_hash = PASSWORD('your_new_password') 
     WHERE username = 'admin';
     ```

2. **Production Setup**
   - Disable error display in `.htaccess`
   - Use strong passwords
   - Consider adding IP whitelist for admin access
   - Enable HTTPS/SSL

## API Endpoint

The dashboard exposes an API endpoint for fetching events:

```
GET /api-events.php?type=webinar&status=upcoming&limit=10
```

Parameters:
- `type`: Filter by event type (webinar, series, other)
- `status`: Filter by status (upcoming, completed, cancelled)
- `limit`: Limit number of results

## Database Structure

### Events Table
- Stores all event/webinar information
- Supports multiple event types
- Tracks status and scheduling

### Admin Users Table
- Stores admin authentication credentials
- Uses password hashing for security

## Troubleshooting

1. **Connection Error**: Check database credentials in `config.php`
2. **Table Not Found**: Run `database_setup.php` again
3. **Login Issues**: Check session is enabled in PHP
4. **Permission Errors**: Ensure PHP has write permissions for database

## Support

For issues or questions, check:
- PHP error logs
- MySQL error logs
- Browser console for JavaScript errors
