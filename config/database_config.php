<?php
/**
 * ------------------------------------------------------------------
 *  Database Configuration
 * ------------------------------------------------------------------
 *  • Edit the default values below *or*
 *  • Define environment variables to override them transparently:
 *
 *      DB_HOST     = 127.0.0.1
 *      DB_PORT     = 8889          ( ⇦ check  MAMP → Preferences → Ports )
 *      DB_NAME     = hkid_app
 *      DB_USER     = root
 *      DB_PASS     = root
 *      DB_CHARSET  = utf8mb4
 *
 *  The helper  Src\Lib\Database::pdo()  will include() this file
 *  and build the DSN automatically.
 */

return [
    'db_host'    => (string) (getenv('DB_HOST')    ?: '127.0.0.1'),
    'db_port'    => (string) (getenv('DB_PORT')    ?: '8889'),      // 8889 = MAMP-Win default
    'db_name'    => (string) (getenv('DB_NAME')    ?: 'hkid_app'),  // ← my schema name
    'db_user'    => (string) (getenv('DB_USER')    ?: 'root'),
    'db_pass'    => (string) (getenv('DB_PASS')    ?: 'root'),
    'db_charset' => (string) (getenv('DB_CHARSET') ?: 'utf8mb4'),
];
