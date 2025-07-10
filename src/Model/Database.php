<?php
// src/Lib/Database.php
namespace Src\Lib; // <<<< ENSURE THIS NAMESPACE IS CORRECT

/**
 * Helper class that returns ONE persistent PDO instance for the entire request,
 * configured from an external file.
 */
class Database
{
    /** @var \PDO|null */
    private static $pdo = null;

    public static function pdo(): \PDO
    {
        if (self::$pdo === null) {
            // PROJECT_ROOT should be defined in public/index.php
            if (!defined('PROJECT_ROOT')) {
                // Fallback if not defined (e.g., for CLI scripts), assuming standard structure
                // This assumes Database.php is in PROJECT_ROOT/src/Lib/
                define('PROJECT_ROOT', dirname(__DIR__, 2)); 
            }

            $configPath = PROJECT_ROOT . '/config/database_config.php'; // Your config file

            if (!file_exists($configPath)) {
                error_log("FATAL: Database configuration file not found at: " . $configPath);
                // For a critical error like this, you might throw an exception or die
                // to prevent the application from continuing in an unstable state.
                throw new \RuntimeException("Database configuration is missing. Please check the server logs.");
            }

            $cfg = require $configPath;

            // Use keys from your database_config.php
            // Provide sensible defaults or ensure your config file has all keys.
            $host    = $cfg['db_host']    ?? '127.0.0.1'; 
            $port    = $cfg['db_port']    ?? '3306';      // Default MySQL port (MAMP Windows often uses 8889, ensure this is in your config)
            $dbname  = $cfg['db_name']    ?? '';
            $user    = $cfg['db_user']    ?? 'root';
            $pass    = $cfg['db_pass']    ?? '';          // Default MAMP password is often 'root' or empty
            $charset = $cfg['db_charset'] ?? 'utf8mb4';

            if (empty($dbname)) {
                error_log("FATAL: Database name (db_name) is not configured in " . $configPath);
                throw new \RuntimeException("Database name is not configured. Please check the server logs.");
            }
            
            // Construct DSN using the port from your config file
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,       // Fetch associative arrays by default
                \PDO::ATTR_EMULATE_PREPARES   => false,                   // Use native prepared statements
            ];

            try {
                self::$pdo = new \PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // Log the detailed error to the server's error log
                error_log("FATAL: Database connection error: " . $e->getMessage() . " (DSN: " . $dsn . ")");
                // Throw a more generic exception to the application
                throw new \RuntimeException('Could not connect to the database. Please check server logs or contact support. Details: ' . $e->getMessage(), (int)$e->getCode(), $e);
            }
        }
        return self::$pdo;
    }

    /** Disallow creating instances directly to enforce singleton via static pdo() method */
    private function __construct() {}
    private function __clone() {}
    // public function __wakeup() {} // For preventing unserialization if needed, typically not for PDO
}
