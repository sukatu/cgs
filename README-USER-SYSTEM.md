# User Login & Booking System

## Setup Instructions

1. **Create User Database Tables**
   - Navigate to: `http://localhost/cgs/database_setup_users.php`
   - This will create:
     - `users` table (for user accounts)
     - `event_registrations` table (for booking events)

2. **User Registration**
   - Users can register at: `http://localhost/cgs/network.html`
   - Fill out the registration form
   - Account is created and user is automatically logged in

3. **User Login**
   - Users can login at: `http://localhost/cgs/login.html`
   - Or use the login form on `network.html`

4. **Booking Events**
   - Logged-in users see "Book This Event" buttons on upcoming events
   - Click to register for an event
   - Registration is saved to the database

5. **User Dashboard**
   - Access at: `http://localhost/cgs/user-dashboard.php`
   - View all registered events
   - Manage profile information

## Features

### User Authentication
- Secure password hashing (bcrypt)
- Session-based authentication
- Email uniqueness validation
- Password strength requirements (minimum 6 characters)

### Event Booking
- One-click booking for logged-in users
- Prevents duplicate registrations
- Tracks registration status (pending, confirmed, cancelled)
- Shows booking history in dashboard

### User Profile
- Stores: name, email, country, city, organization, role
- Optional: bio, LinkedIn URL, interests
- Profile viewable in dashboard

## Database Tables

### users
- id, name, email, password_hash
- phone, country, city, organization, role
- interests, bio, linkedin_url
- email_verified, created_at, updated_at

### event_registrations
- id, user_id, event_id
- registration_date, status
- notes
- Foreign keys to users and events tables

## Security

- Passwords are hashed using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- CSRF protection recommended for production

## Testing

1. Register a new user at `network.html`
2. Login with credentials
3. Browse events and click "Book This Event"
4. View bookings in `user-dashboard.php`
