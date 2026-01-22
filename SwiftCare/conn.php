<?php
/**
 * Database Connection File
 * 
 * For Infinity Free Hosting Deployment:
 * Update the following variables with your hosting provider's database credentials:
 * - DB_HOST: Usually 'localhost' or your host provider's database server
 * - DB_NAME: Your database name (e.g., 'epiz_xxxxx_swiftcare_db')
 * - DB_USER: Your database username (e.g., 'epiz_xxxxx')
 * - DB_PASS: Your database password
 * 
 * You can find these credentials in your Infinity Free control panel under "MySQL Databases"
 */

// Database configuration
// LIVE SETTINGS (InfinityFree)
define('DB_HOST', ''); 
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

// Create database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

