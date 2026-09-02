<?php
/**
 * Database Configuration
 * For Hostinger Business Web Hosting
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'u288510777_sbaldwin');
define('DB_PASSWORD', 'K260825Clay');
define('DB_NAME', 'u288510777_insurance_form');

// Site configuration
define('SITE_URL', 'https://baldwin.claysites.com');
define('ADMIN_EMAIL', 'aaclay1@gmail.com');

// Email configuration (for verification codes)
define('MAIL_FROM', 'noreply@baldwin.claysites.com');
define('MAIL_FROM_NAME', 'Insurance Form');

// Security
define('SESSION_TIMEOUT', 3600);  // 1 hour
define('TOKEN_EXPIRY', 600);  // 10 minutes for email verification

// Create database connection
global $pdo;
$pdo = null;

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        )
    );
} catch (PDOException $e) {
    // Don't die - let the calling script handle it
    error_log('PDO Connection Error: ' . $e->getMessage());
    $pdo = null;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
