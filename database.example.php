<?php
/**
 * MiniMarket POS — database connection example
 *
 * Setup:
 * 1. Copy this file to includes/database.php
 * 2. Fill in your local MySQL / MariaDB credentials
 * 3. Import docs/database-schema.sql in phpMyAdmin (XAMPP)
 *
 * Do not commit includes/database.php — it may contain your password.
 * The running app currently stores data in data/*.json by default.
 */

return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'minimarket_pos',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
