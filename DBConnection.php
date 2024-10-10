<?php
// Ensure the 'db' directory exists
if (!is_dir(__DIR__ . '/db')) {
    mkdir(__DIR__ . '/db', 0777, true); // Create 'db' directory with proper permissions
}

// Define constants for the database file and timezone
if (!defined('db_file')) define('db_file', __DIR__ . '/db/psms_db.db'); // Corrected the path for SQLite file
if (!defined('tZone')) define('tZone', "Asia/Manila");
if (!defined('dZone')) define('dZone', ini_get('date.timezone'));

// Custom MD5 function for hashing passwords
function my_udf_md5($string) {
    return md5($string);
}

// Class for database connection and schema setup
Class DBConnection extends SQLite3 {
    protected $db;
    
    function __construct() {
        // Open SQLite database file
        $this->open(db_file);
        // Create a custom MD5 function in SQLite
        $this->createFunction('md5', 'my_udf_md5');
        // Enable foreign keys in SQLite
        $this->exec("PRAGMA foreign_keys = ON;");
        
        // Create 'user_list' table
        $this->exec("CREATE TABLE IF NOT EXISTS `user_list` (
            `user_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` TEXT NOT NULL,  -- Changed from INTEGER to TEXT for storing names
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL DEFAULT 1,
            `status` INTEGER NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // User Comment:
        // Type = [1 = Administrator, 2 = Cashier]
        // Status = [1 = Active, 2 = Inactive]

        // Create 'petrol_type_list' table
        $this->exec("CREATE TABLE IF NOT EXISTS `petrol_type_list` (
            `petrol_type_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `price` REAL NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1
        )");

        // Create 'customer_list' table
        $this->exec("CREATE TABLE IF NOT EXISTS `customer_list` (
            `customer_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `customer_code` TEXT NOT NULL,
            `fullname` TEXT NOT NULL,
            `contact` TEXT NOT NULL,
            `email` TEXT NOT NULL,
            `address` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1
        )");

        // Create 'transaction_list' table
        $this->exec("CREATE TABLE IF NOT EXISTS `transaction_list` (
            `transaction_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `customer_id` INTEGER NOT NULL,
            `receipt_no` TEXT NOT NULL,
            `petrol_type_id` INTEGER NOT NULL,  -- Changed to INTEGER for proper foreign key reference
            `price` REAL NOT NULL,
            `liter` REAL NOT NULL,
            `amount` REAL NOT NULL,
            `discount` REAL NOT NULL,
            `total` REAL NOT NULL DEFAULT 0,
            `tendered_amount` REAL NOT NULL DEFAULT 0,
            `change` REAL NOT NULL DEFAULT 0,
            `type` INTEGER NOT NULL DEFAULT 1,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `user_id` INTEGER NOT NULL DEFAULT 1,
            FOREIGN KEY(`user_id`) REFERENCES `user_list`(`user_id`) ON DELETE SET NULL,
            FOREIGN KEY(`petrol_type_id`) REFERENCES `petrol_type_list`(`petrol_type_id`) ON DELETE SET NULL,
            FOREIGN KEY(`customer_id`) REFERENCES `customer_list`(`customer_id`) ON DELETE SET NULL
        )");

        // Create 'debt_list' table
        $this->exec("CREATE TABLE IF NOT EXISTS `debt_list` (
            `debt_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `transaction_id` INTEGER NOT NULL,
            `customer_id` INTEGER NOT NULL,
            `amount` REAL NOT NULL DEFAULT 0,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`transaction_id`) REFERENCES `transaction_list`(`transaction_id`) ON DELETE CASCADE,
            FOREIGN KEY(`customer_id`) REFERENCES `customer_list`(`customer_id`) ON DELETE CASCADE
        )");

        // Create 'payment_list' table
        $this->exec("CREATE TABLE IF NOT EXISTS `payment_list` (
            `payment_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `payment_code` TEXT NOT NULL,
            `customer_id` INTEGER NOT NULL,
            `amount` REAL NOT NULL DEFAULT 0,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`customer_id`) REFERENCES `customer_list`(`customer_id`) ON DELETE CASCADE
        )");

        // Insert default admin user if not already present
        $this->exec("INSERT OR IGNORE INTO `user_list` 
                     (user_id, fullname, username, password, type, status, date_created) 
                     VALUES (1, 'Administrator', 'admin', md5('admin123'), 1, 1, CURRENT_TIMESTAMP)");
    }
    
    function __destruct() {
        $this->close(); // Close database connection when the object is destroyed
    }
}

// Create a new database connection instance
$conn = new DBConnection();
?>
