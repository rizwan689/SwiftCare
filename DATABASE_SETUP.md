# SwiftCare Database Setup Guide

## Prerequisites
- XAMPP installed and running
- MySQL/MariaDB service running in XAMPP

## Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service

## Step 2: Create the Database

### Option A: Using phpMyAdmin (Recommended)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "New" in the left sidebar to create a new database
3. Enter database name: `swiftcare_db`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Select the `swiftcare_db` database from the left sidebar
7. Click on the "Import" tab at the top
8. Click "Choose File" and select `database/swiftcare_db.sql`
9. Click "Go" to import the database structure

### Option B: Using MySQL Command Line
1. Open Command Prompt or Terminal
2. Navigate to your project directory: `cd C:\xampp\htdocs\SwiftCare`
3. Run the following command:
   ```
   mysql -u root -p < database/swiftcare_db.sql
   ```
   (Press Enter when prompted for password, or enter your MySQL root password if you have one set)

## Step 3: Verify Database Connection
The database connection is configured in `config/db.php`:
- **Host:** localhost
- **Database:** swiftcare_db
- **Username:** root
- **Password:** (empty by default)

If your MySQL root password is different, edit `config/db.php` and update the password.

## Step 4: Test the Application
1. Open your browser and go to: `http://localhost/SwiftCare`
2. Try registering a new user
3. Try logging in

## Default Admin Account
After importing the database, you can login with:
- **Email:** admin@swiftcare.com
- **Password:** admin123

**⚠️ IMPORTANT:** Change the admin password after first login!

## Database Tables Created
1. **users** - Stores patients, doctors, and admin users
2. **schedules** - Stores doctor availability schedules
3. **sessions** - Stores patient appointments/bookings
4. **settings** - Stores clinic settings

## Troubleshooting

### Database connection failed
- Make sure MySQL service is running in XAMPP
- Check if the database name in `config/db.php` matches the created database
- Verify MySQL username and password in `config/db.php`

### Tables not found
- Make sure you imported the SQL file correctly
- Check if the database `swiftcare_db` exists in phpMyAdmin

### Session errors
- Make sure `session_start()` is called before using `$_SESSION`
- Check PHP error logs in XAMPP
