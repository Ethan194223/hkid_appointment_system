<?php

// src/core.php

/**
 * Core application bootstrap file.
 */

// Strict error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production

// Default Timezone
date_default_timezone_set('Asia/Hong_Kong');

// Define Application Constants
// APPROOT is the absolute path to your project's root directory
define('APPROOT', dirname(__DIR__)); // e.g., /Applications/MAMP/htdocs/HKID_APPOINTMENT_SYSTEM

define('CONFIG_PATH', APPROOT . '/config');
define('SRC_PATH', APPROOT . '/src'); // e.g., /Applications/MAMP/htdocs/HKID_APPOINTMENT_SYSTEM/src
define('PUBLIC_PATH', APPROOT . '/public');
define('LIB_PATH', SRC_PATH . '/Lib');
define('MODEL_PATH', SRC_PATH . '/Model');
define('VIEW_PATH', SRC_PATH . '/View');
define('CONTROLLER_PATH', SRC_PATH . '/Controller');

// Determine Base URL (points to your public folder URL)
// For MAMP, if your project is 'HKID_APPOINTMENT_SYSTEM' in htdocs,
// and you access it via localhost:8888/HKID_APPOINTMENT_SYSTEM/public/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// This assumes your project root (HKID_APPOINTMENT_SYSTEM) is directly under htdocs.
// And your web server is configured so /HKID_APPOINTMENT_SYSTEM/public/ is the entry.
$subfolder = str_replace($_SERVER['DOCUMENT_ROOT'], '', APPROOT); // Path relative to htdocs
define('BASE_URL', $protocol . $host . $subfolder . '/public/');

// Load Composer Autoloader
if (file_exists(APPROOT . '/vendor/autoload.php')) {
    require_once APPROOT . '/vendor/autoload.php';
    // echo "DEBUG: Composer autoload.php included successfully.<br>"; // Temporary debug
} else {
    die("CRITICAL ERROR: Composer autoload.php not found. Please run 'composer install' or 'composer dump-autoload' in your project root: " . APPROOT);
}

// Load Application Configuration (e.g., database)
if (file_exists(CONFIG_PATH . '/database_config.php')) {
    $GLOBALS['config'] = require CONFIG_PATH . '/database_config.php';
} else {
    // Not dying here, as some parts might not need DB immediately
    // echo "NOTICE: Database configuration file (database_config.php) not found in " . CONFIG_PATH . "<br>";
}

// Include Global Helper Functions (if any)
if (file_exists(LIB_PATH . '/functions.php')) {
    require_once LIB_PATH . '/functions.php';
}

?>