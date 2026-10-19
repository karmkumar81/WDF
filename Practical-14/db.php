<?php
/**
 * Practical 14: Database Connection Helper
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'studenthub');

function getDB(): mysqli {
    static $conn = null;
    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            $conn->set_charset("utf8mb4");
        } catch (mysqli_sql_exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    return $conn;
}
